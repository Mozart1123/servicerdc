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
     * Each category is processed independently — no global transaction.
     * If one fails the others are still saved.
     */
    private function storeBatchArray(Request $request)
    {
        @set_time_limit(0); // attempt unlimited; ignored on locked hosts but harmless
        ini_set('max_execution_time', 0);

        $request->validate([
            'categories'               => 'required|array|min:1|max:50',
            'categories.*.name'        => 'required|string|max:255',
            'categories.*.description' => 'nullable|string',
            'categories.*.services'    => 'nullable|string',
            'categories.*.image'       => 'nullable|image',
        ]);

        $items          = array_values($request->input('categories', []));
        $fileCategories = $request->file('categories', []);
        $createdCount   = 0;
        $errors         = [];

        // Pre-load all existing slugs once to avoid repeated SELECT queries
        $existingSlugs = Category::pluck('slug')->flip()->toArray();

        foreach ($items as $index => $catData) {
            $name = trim($catData['name'] ?? '');
            if ($name === '') {
                continue;
            }

            try {
                // Generate unique slug without DB loop
                $slug = $this->uniqueSlugFromCache($name, $existingSlugs);
                $existingSlugs[$slug] = true; // mark as used for next iteration

                $imagePath = null;

                // Try both key formats for uploaded files
                if ($request->hasFile("categories.{$index}.image")) {
                    $imagePath = $request->file("categories.{$index}.image")->store('categories', 'public');
                } elseif (is_array($fileCategories) && isset($fileCategories[$index]['image'])) {
                    $file = $fileCategories[$index]['image'];
                    if ($file && $file->isValid()) {
                        $imagePath = $file->store('categories', 'public');
                    }
                }

                $category = Category::create([
                    'name'        => $name,
                    'slug'        => $slug,
                    'image'       => $imagePath,
                    'description' => isset($catData['description']) ? trim($catData['description']) : null,
                ]);

                if (!empty($catData['services'])) {
                    $this->createServiceTypes($category->id, $catData['services']);
                }

                $createdCount++;

            } catch (\Throwable $e) {
                $errors[] = "« {$name} » : " . $e->getMessage();
                \Log::error('Batch category creation failed for: ' . $name, ['error' => $e->getMessage()]);
            }
        }

        if ($createdCount === 0 && !empty($errors)) {
            return redirect()->back()
                             ->withErrors(['batch' => 'Aucune catégorie n\'a pu être créée. Erreurs : ' . implode(' | ', $errors)])
                             ->withInput();
        }

        $msg = "{$createdCount} catégorie(s) créée(s) avec succès.";
        if (!empty($errors)) {
            $msg .= ' Attention : ' . count($errors) . ' catégorie(s) ignorée(s) (voir les logs).'; 
        }

        return redirect()->route('admin.categories.index')->with('success', $msg);
    }

    /**
     * Store bulk text import.
     * Each category is inserted independently — no global rollback risk.
     */
    private function storeTextImport(Request $request)
    {
        @set_time_limit(0);
        ini_set('max_execution_time', 0);

        $request->validate([
            'import_text' => 'required|string',
        ]);

        $parsed = $this->parseStructuredText($request->input('import_text'));

        if (empty($parsed)) {
            return redirect()->back()
                             ->withErrors(['import_text' => 'Aucune catégorie valide n\'a pu être détectée dans le texte fourni. Veuillez utiliser le format ## Nom de la catégorie.'])
                             ->withInput();
        }

        $existingSlugs = Category::pluck('slug')->flip()->toArray();
        $createdCount  = 0;
        $errors        = [];
        $now           = now();

        foreach ($parsed as $item) {
            try {
                $slug = $this->uniqueSlugFromCache($item['name'], $existingSlugs);
                $existingSlugs[$slug] = true;

                $category = Category::create([
                    'name'        => $item['name'],
                    'slug'        => $slug,
                    'description' => $item['description'] ?: null,
                ]);

                if (!empty($item['services'])) {
                    $serviceRecords = [];
                    foreach ($item['services'] as $serviceTitle) {
                        $serviceRecords[] = [
                            'category_id' => $category->id,
                            'title'       => $serviceTitle,
                            'created_at'  => $now,
                            'updated_at'  => $now,
                        ];
                    }
                    ServiceType::insert($serviceRecords);
                }

                $createdCount++;

            } catch (\Throwable $e) {
                $errors[] = "« {$item['name']} »";
                \Log::error('Text import category failed: ' . $item['name'], ['error' => $e->getMessage()]);
            }
        }

        $msg = "Importation réussie : {$createdCount} catégorie(s) et leurs sous-services ont été créés.";
        if (!empty($errors)) {
            $msg .= ' Ignorées : ' . implode(', ', $errors);
        }

        return redirect()->route('admin.categories.index')->with('success', $msg);
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
            foreach (explode("\n", str_replace("\r\n", "\n", $request->input('services'))) as $title) {
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

        $now     = now();
        $records = [];

        foreach (explode("\n", str_replace("\r\n", "\n", $rawServices)) as $title) {
            $title = trim($title);
            if ($title !== '') {
                $records[] = [
                    'category_id' => $categoryId,
                    'title'       => $title,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        if (!empty($records)) {
            ServiceType::insert($records);
        }
    }

    /**
     * Legacy slug generator (1 DB query per slug — used when cache unavailable).
     */
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
     * Slug generator using pre-loaded cache — zero extra DB queries per item.
     *
     * @param array<string, mixed> $existingSlugs  Pass by reference to accumulate within a loop.
     */
    private function uniqueSlugFromCache(string $name, array &$existingSlugs): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i    = 1;
        while (array_key_exists($slug, $existingSlugs)) {
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
