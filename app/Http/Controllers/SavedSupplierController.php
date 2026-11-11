<?php

namespace App\Http\Controllers;

use App\Models\SavedSupplier;
use App\Models\Supplier;
use App\Support\SqlLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SavedSupplierController extends Controller
{
    /**
     * Display the saved suppliers and procurement collections page.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $collections = $user->procurementCollections()
            ->withCount('savedSuppliers')
            ->latest()
            ->get();

        $query = $user->savedSuppliers()
            ->with(['supplier.tags', 'supplier.certifications.certification', 'collection'])
            ->latest();

        $activeCollectionId = $request->query('collection');
        $activeCollection = null;

        if ($activeCollectionId === 'unassigned') {
            $query->whereNull('collection_id');
        } elseif (is_numeric($activeCollectionId)) {
            $query->where('collection_id', (int) $activeCollectionId);
            $activeCollection = $collections->firstWhere('id', (int) $activeCollectionId);
        }

        if ($request->filled('q') && is_string($request->query('q'))) {
            $pattern = SqlLike::pattern($request->query('q'));
            if ($pattern !== null) {
                $query->whereHas('supplier', function ($q) use ($pattern) {
                    $q->where('name', 'like', $pattern)
                        ->orWhere('industry_category', 'like', $pattern)
                        ->orWhere('city', 'like', $pattern)
                        ->orWhere('country', 'like', $pattern);
                });
            }
        }

        $savedSuppliers = $query->paginate(12)->withQueryString();

        $totalSavedCount = $user->savedSuppliers()->count();
        $unassignedCount = $user->savedSuppliers()->whereNull('collection_id')->count();

        // Collect all supplier IDs in current view for quick comparison
        $currentSupplierIds = $savedSuppliers->pluck('supplier_id')->filter()->unique()->values()->toArray();

        return view('saved-suppliers.index', compact(
            'savedSuppliers',
            'collections',
            'activeCollectionId',
            'activeCollection',
            'totalSavedCount',
            'unassignedCount',
            'currentSupplierIds'
        ));
    }

    /**
     * Toggle bookmark/saved status of a supplier.
     */
    public function toggle(Supplier $supplier): RedirectResponse
    {
        $existing = SavedSupplier::where('user_id', Auth::id())
            ->where('supplier_id', $supplier->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = "Pemasok '{$supplier->name}' dihapus dari daftar tersimpan.";
        } else {
            SavedSupplier::create([
                'user_id' => Auth::id(),
                'supplier_id' => $supplier->id,
            ]);
            $message = "Pemasok '{$supplier->name}' berhasil disimpan ke daftar Anda.";
        }

        return back()->with('success', $message);
    }

    /**
     * Move a saved supplier to a specific collection.
     */
    public function move(Request $request, SavedSupplier $savedSupplier): RedirectResponse
    {
        if ($savedSupplier->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah ke data simpan.');
        }

        $validated = $request->validate([
            'collection_id' => ['nullable', 'integer'],
        ]);

        $collectionId = $validated['collection_id'] ?? null;

        if ($collectionId) {
            $collection = Auth::user()->procurementCollections()->find($collectionId);
            if (! $collection) {
                return back()->withErrors(['collection_id' => 'Proyek pengadaan tidak valid.']);
            }
        }

        $savedSupplier->collection_id = $collectionId;
        $savedSupplier->save();

        $collectionName = $savedSupplier->collection ? $savedSupplier->collection->name : 'Tanpa Proyek';

        return back()->with('success', "Pemasok dialihkan ke: '{$collectionName}'.");
    }

    /**
     * Remove a saved supplier directly.
     */
    public function destroy(SavedSupplier $savedSupplier): RedirectResponse
    {
        if ($savedSupplier->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah ke data simpan.');
        }

        $name = $savedSupplier->supplier->name ?? 'Pemasok';
        $savedSupplier->delete();

        return back()->with('success', "Pemasok '{$name}' telah dihapus dari daftar tersimpan.");
    }
}
