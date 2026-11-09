<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificationAdminController extends Controller
{
    /**
     * Display a listing of certifications.
     */
    public function index(Request $request): View
    {
        $query = Certification::query()->withCount('suppliers')->orderBy('name');

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('category', 'like', "%{$keyword}%");
            });
        }

        $certifications = $query->paginate(15);

        return view('admin.certifications.index', compact('certifications'));
    }

    /**
     * Show the form for creating a new certification.
     */
    public function create(): View
    {
        $existingCategories = Certification::select('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.certifications.create', compact('existingCategories'));
    }

    /**
     * Store a newly created certification in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:certifications,name'],
            'category' => ['required', 'string', 'max:100'],
        ]);

        Certification::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
        ]);

        return redirect()->route('admin.certifications.index')
            ->with('success', "Sertifikasi '{$validated['name']}' berhasil ditambahkan ke daftar master data.");
    }

    /**
     * Show the form for editing the certification.
     */
    public function edit(Certification $certification): View
    {
        $existingCategories = Certification::select('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.certifications.edit', compact('certification', 'existingCategories'));
    }

    /**
     * Update the certification in storage.
     */
    public function update(Request $request, Certification $certification): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:certifications,name,'.$certification->id],
            'category' => ['required', 'string', 'max:100'],
        ]);

        $certification->name = $validated['name'];
        $certification->category = $validated['category'];
        $certification->save();

        return redirect()->route('admin.certifications.index')
            ->with('success', "Sertifikasi '{$certification->name}' berhasil diperbarui.");
    }

    /**
     * Remove the certification from storage.
     */
    public function destroy(Certification $certification): RedirectResponse
    {
        $name = $certification->name;

        // Delete relations to suppliers
        $certification->suppliers()->delete();
        $certification->delete();

        return redirect()->route('admin.certifications.index')
            ->with('success', "Sertifikasi '{$name}' dan catatan asosiasi pemasok berhasil dihapus.");
    }
}
