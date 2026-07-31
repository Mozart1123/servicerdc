<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::with('serviceTypes')->latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store newly created categories (single, batch array, or text import).
     */
    public function store(Request $request)
    {
        // MODE 1: Import par Texte Structuré
        if ($request->filled('import_text')) {
            return $this->storeTextImport($request);
        }

        // MODE 2: Lot Répétable (Array categories)
        if ($request->has('categories') && is_array($request->input('categories'))) {
            return $this->storeBatchArray($request);
        }

        // MODE 3: Single Category Submission (fallback)
        return $this->storeSingle($request);
    }

    /**
     * Store single category.
     */
    private function storeSingle(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'services'    => 'nullable|string',
            'image'       => 'nullable|image',
        ]);

        $category = DB::transaction(function () use ($request) {
            $slug      = $this->uniqueSlug($request->input('name'));
            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
            }

            $category = Category::create([
                'name'        => $request->input('name'),
                'slug'        => $slug,
                'image'       => $imagePath,
                'description' => $request->input('description'),
            ]);

            $this->createServiceTypes($category->id, $request->input('services'));

            return $category;
        });

        return redirect()->route('admin.categories.index')
                         ->with('success', "Catégorie « {$category->name} » créée avec succès.");
    }

    /**
     * Store batch categories array.
     */
    private function storeBatchArray(Request $request)
    {
        $request->validate([
            'categories'               => 'required|array|min:1|max:20',
            'categories.*.name'        => 'required|string|max:255',
            'categories.*.description' => 'nullable|string',
            'categories.*.services'    => 'nullable|string',
            'categories.*.image'       => 'nullable|image',
        ]);

        $createdCount = DB::transaction(function () use ($request) {
            $count = 0;
            $items = $request->input('categories');

            foreach ($items as $index => $catData) {
                if (empty(trim($catData['name'] ?? ''))) {
                    continue;
                }

                $slug      = $this->uniqueSlug($catData['name']);
                $imagePath = null;

                if ($request->hasFile("categories.{$index}.image")) {
                    $imagePath = $request->file("categories.{$index}.image")->store('categories', 'public');
                }

                $category = Category::create([
                    'name'        => trim($catData['name']),
                    'slug'        => $slug,
                    'image'       => $imagePath,
                    'description' => isset($catData['description']) ? trim($catData['description']) : null,
                ]);

                if (!empty($catData['services'])) {
                    $this->createServiceTypes($category->id, $catData['services']);
                }

                $count++;
            }

            return $count;
        });

        return redirect()->route('admin.categories.index')
                         ->with('success', "{$createdCount} catégorie(s) créée(s) avec succès.");
    }

    /**
     * Store bulk text import.
     */
    private function storeTextImport(Request $request)
    {
        $request->validate([
            'import_text' => 'required|string',
        ]);

        $parsed = $this->parseStructuredText($request->input('import_text'));

        if (empty($parsed)) {
            return redirect()->back()
                             ->withErrors(['import_text' => 'Aucune catégorie valide n\'a pu être détectée dans le texte fourni. Veuillez utiliser le format ## Nom de la catégorie.'])
                             ->withInput();
        }

        $createdCount = DB::transaction(function () use ($parsed) {
            $count = 0;
            foreach ($parsed as $item) {
                $slug = $this->uniqueSlug($item['name']);

                $category = Category::create([
                    'name'        => $item['name'],
                    'slug'        => $slug,
                    'description' => $item['description'] ?: null,
                ]);

                foreach ($item['services'] as $serviceTitle) {
                    ServiceType::create([
                        'category_id' => $category->id,
                        'title'       => $serviceTitle,
                    ]);
                }

                $count++;
            }
            return $count;
        });

        return redirect()->route('admin.categories.index')
                         ->with('success', "Importation réussie : {$createdCount} catégorie(s) et leurs sous-services ont été créés.");
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'services'    => 'nullable|string',
            'image'       => 'nullable|image',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update(array_diff_key($validated, ['services' => 1]));

        if ($request->has('services')) {
            $existingIds = [];
            foreach (explode("\n", $request->input('services')) as $title) {
                $title = trim($title);
                if ($title !== '') {
                    $st = ServiceType::updateOrCreate(
                        ['category_id' => $category->id, 'title' => $title]
                    );
                    $existingIds[] = $st->id;
                }
            }
            $category->serviceTypes()->whereNotIn('id', $existingIds)->delete();
        }

        return redirect()->route('admin.categories.index')
                         ->with('success', "Catégorie « {$category->name} » mise à jour avec succès.");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Catégorie supprimée.');
    }

    // ==========================================
    // Helpers
    // ==========================================

    private function createServiceTypes(int $categoryId, ?string $rawServices): void
    {
        if (empty($rawServices)) {
            return;
        }

        foreach (explode("\n", $rawServices) as $title) {
            $title = trim($title);
            if ($title !== '') {
                ServiceType::create([
                    'category_id' => $categoryId,
                    'title'       => $title,
                ]);
            }
        }
    }

    private function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i    = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    /**
     * Parse structured markdown-like text into array of categories & sub-services.
     */
    private function parseStructuredText(string $text): array
    {
        $lines = explode("\n", str_replace("\r\n", "\n", $text));
        $categories = [];
        $currentCat = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (empty($trimmed)) {
                continue;
            }

            // Category header: ## Name or # Name
            if (str_starts_with($trimmed, '##') || str_starts_with($trimmed, '#')) {
                if ($currentCat) {
                    $categories[] = $currentCat;
                }
                $name = trim(ltrim($trimmed, '#'));
                $currentCat = [
                    'name'        => $name,
                    'description' => '',
                    'services'    => [],
                ];
                continue;
            }

            // Sub-service item: - Title or * Title
            if (str_starts_with($trimmed, '-') || str_starts_with($trimmed, '*')) {
                if ($currentCat) {
                    $serviceTitle = trim(ltrim($trimmed, '-*'));
                    if (!empty($serviceTitle)) {
                        $currentCat['services'][] = $serviceTitle;
                    }
                }
                continue;
            }

            // Description line
            if ($currentCat) {
                if (!empty($currentCat['description'])) {
                    $currentCat['description'] .= ' ' . $trimmed;
                } else {
                    $currentCat['description'] = $trimmed;
                }
            }
        }

        if ($currentCat) {
            $categories[] = $currentCat;
        }

        return $categories;
    }
}
