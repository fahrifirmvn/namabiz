<?php

namespace App\Http\Controllers;

use App\Models\ProcurementCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementCollectionController extends Controller
{
    /**
     * Store a newly created procurement collection for the authenticated user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $collection = Auth::user()->procurementCollections()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('saved.index', ['collection' => $collection->id])
            ->with('success', "Proyek pengadaan '{$collection->name}' berhasil dibuat.");
    }

    /**
     * Update the specified procurement collection.
     */
    public function update(Request $request, ProcurementCollection $collection): RedirectResponse
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah ke proyek pengadaan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $collection->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('saved.index', ['collection' => $collection->id])
            ->with('success', "Proyek pengadaan '{$collection->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified procurement collection.
     */
    public function destroy(ProcurementCollection $collection): RedirectResponse
    {
        if ($collection->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah ke proyek pengadaan.');
        }

        $name = $collection->name;
        $collection->delete();

        return redirect()->route('saved.index')
            ->with('success', "Proyek pengadaan '{$name}' telah dihapus. Vendor terkait tetap tersimpan di arsip.");
    }
}
