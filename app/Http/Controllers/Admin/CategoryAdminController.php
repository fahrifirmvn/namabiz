<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndustryCategory;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryAdminController extends Controller
{
    /**
     * Display a listing of industry categories.
     */
    public function index(Request $request): View
    {
        $query = IndustryCategory::query()->withCount('suppliers')->orderBy('name');

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        $categories = $query->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new industry category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:industry_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug ?: 'category-'.Str::random(5);
        $counter = 1;
        while (IndustryCategory::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        IndustryCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori industri '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the category.
     */
    public function edit(IndustryCategory $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the category in storage.
     */
    public function update(Request $request, IndustryCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:industry_categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldName = $category->name;
        $category->name = $validated['name'];
        $category->slug = Str::slug($validated['name']);
        $category->description = $validated['description'] ?? null;
        $category->save();

        // Sync existing suppliers using the old name
        if ($oldName !== $validated['name']) {
            Supplier::where('industry_category', $oldName)->update([
                'industry_category' => $validated['name'],
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori industri '{$category->name}' berhasil diperbarui.");
    }

    /**
     * Remove the category from storage.
     */
    public function destroy(IndustryCategory $category): RedirectResponse
    {
        $name = $category->name;
        $suppliersCount = $category->suppliers()->count();

        if ($suppliersCount > 0) {
            // Reassign suppliers to general category
            Supplier::where('industry_category', $name)->update([
                'industry_category' => 'Raw Material Supplier',
            ]);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
