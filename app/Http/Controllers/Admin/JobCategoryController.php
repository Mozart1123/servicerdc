<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JobCategoryController extends Controller
{
    /**
     * Display a listing of the job categories.
     */
    public function index()
    {
        $jobCategories = JobCategory::withCount('jobOffers')->latest()->paginate(20);
        return view('admin.job-categories.index', compact('jobCategories'));
    }

    /**
     * Store newly created job categories (single or batch array).
     */
    public function store(Request $request)
    {
        if ($request->has('categories') && is_array($request->input('categories'))) {
            return $this->storeBatchArray($request);
        }

        return $this->storeSingle($request);
    }

    /**
     * Store single job category.
     */
    private function storeSingle(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image',
        ]);

        $slug      = $this->uniqueSlug($request->input('name'));
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('job_categories', 'public');
        }

        $category = JobCategory::create([
            'name'        => $request->input('name'),
            'slug'        => $slug,
            'image'       => $imagePath,
            'description' => $request->input('description'),
        ]);

        return redirect()->route('admin.job-categories.index')
                         ->with('success', "Catégorie d'emploi « {$category->name} » créée avec succès.");
    }

    /**
     * Store batch job categories array.
     * Each category is processed independently — no global transaction.
     * If one fails the others are still saved.
     */
    private function storeBatchArray(Request $request)
    {
        @set_time_limit(0);
        ini_set('max_execution_time', 0);

        $request->validate([
            'categories'               => 'required|array|min:1|max:50',
            'categories.*.name'        => 'required|string|max:255',
            'categories.*.description' => 'nullable|string',
            'categories.*.image'       => 'nullable|image',
        ]);

        $items          = array_values($request->input('categories', []));
        $fileCategories = $request->file('categories', []);
        $createdCount   = 0;
        $errors         = [];

        // Pre-load all existing slugs once to avoid repeated SELECT queries
        $existingSlugs = JobCategory::pluck('slug')->flip()->toArray();

        foreach ($items as $index => $catData) {
            $name = trim($catData['name'] ?? '');
            if ($name === '') {
                continue;
            }

            try {
                $slug = $this->uniqueSlugFromCache($name, $existingSlugs);
                $existingSlugs[$slug] = true;

                $imagePath = null;

                if ($request->hasFile("categories.{$index}.image")) {
                    $imagePath = $request->file("categories.{$index}.image")->store('job_categories', 'public');
                } elseif (is_array($fileCategories) && isset($fileCategories[$index]['image'])) {
                    $file = $fileCategories[$index]['image'];
                    if ($file && $file->isValid()) {
                        $imagePath = $file->store('job_categories', 'public');
                    }
                }

                JobCategory::create([
                    'name'        => $name,
                    'slug'        => $slug,
                    'image'       => $imagePath,
                    'description' => isset($catData['description']) ? trim($catData['description']) : null,
                ]);

                $createdCount++;

            } catch (\Throwable $e) {
                $errors[] = "« {$name} » : " . $e->getMessage();
                \Log::error('Batch job category creation failed for: ' . $name, ['error' => $e->getMessage()]);
            }
        }

        if ($createdCount === 0 && !empty($errors)) {
            return redirect()->back()
                             ->withErrors(['batch' => 'Aucune catégorie n\'a pu être créée. Erreurs : ' . implode(' | ', $errors)])
                             ->withInput();
        }

        $msg = "{$createdCount} catégorie(s) d'emploi créée(s) avec succès.";
        if (!empty($errors)) {
            $msg .= ' Attention : ' . count($errors) . ' catégorie(s) ignorée(s) (voir les logs).';
        }

        return redirect()->route('admin.job-categories.index')->with('success', $msg);
    }

    /**
     * Update the specified job category.
     */
    public function update(Request $request, JobCategory $jobCategory)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:job_categories,slug,' . $jobCategory->id,
            'description' => 'nullable|string',
            'image'       => 'nullable|image',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('image')) {
            if ($jobCategory->image) {
                Storage::disk('public')->delete($jobCategory->image);
            }
            $validated['image'] = $request->file('image')->store('job_categories', 'public');
        }

        $jobCategory->update($validated);

        // Keep the denormalized `category` text on already-linked job offers
        // in sync with the category's (possibly renamed) name.
        $jobCategory->jobOffers()->update(['category' => $jobCategory->name]);

        return redirect()->route('admin.job-categories.index')
                         ->with('success', "Catégorie d'emploi « {$jobCategory->name} » mise à jour avec succès.");
    }

    /**
     * Remove the specified job category from storage.
     *
     * Job offers using it are NOT deleted — they simply lose the structured
     * link (job_category_id -> NULL via the FK's nullOnDelete) while keeping
     * their original `category` text intact everywhere it's displayed.
     */
    public function destroy(JobCategory $jobCategory)
    {
        if ($jobCategory->image) {
            Storage::disk('public')->delete($jobCategory->image);
        }
        $jobCategory->delete();

        return redirect()->route('admin.job-categories.index')
                         ->with('success', "Catégorie d'emploi supprimée.");
    }

    // ==========================================
    // Helpers
    // ==========================================

    private function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i    = 1;
        while (JobCategory::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    /**
     * @param array<string, mixed> $existingSlugs Pass by reference to accumulate within a loop.
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
}
