<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierAdminController extends Controller
{
    /**
     * Display a listing of suppliers for admin.
     */
    public function index(Request $request)
    {
        $query = Supplier::query()->latest();

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('industry_category', 'like', "%{$keyword}%")
                    ->orWhere('country', 'like', "%{$keyword}%");
            });
        }

        $suppliers = $query->paginate(15);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'industry_category' => ['required', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:255'],
            'moq' => ['nullable', 'string', 'max:100'],
            'production_capacity' => ['nullable', 'string', 'max:100'],
            'avg_lead_time' => ['nullable', 'string', 'max:100'],
            'is_verified' => ['nullable', 'boolean'],
            'logo_url' => ['nullable', 'string'],
            'logo_file' => ['nullable', 'image', 'max:2048'],
        ]);

        $supplier->name = $validated['name'];
        $supplier->tagline = $validated['tagline'] ?? $supplier->tagline;
        $supplier->description = $validated['description'] ?? $supplier->description;
        $supplier->country = $validated['country'];
        $supplier->city = $validated['city'] ?? $supplier->city;
        $supplier->industry_category = $validated['industry_category'];
        $supplier->website = $validated['website'] ?? $supplier->website;
        $supplier->moq = $validated['moq'] ?? $supplier->moq;
        $supplier->production_capacity = $validated['production_capacity'] ?? $supplier->production_capacity;
        $supplier->avg_lead_time = $validated['avg_lead_time'] ?? $supplier->avg_lead_time;
        $supplier->is_verified = $request->has('is_verified');

        // Handle logo file upload or URL
        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('suppliers', 'public');
            $supplier->logo = $path;
        } elseif (! empty($validated['logo_url'])) {
            $supplier->logo = $validated['logo_url'];
        }

        $supplier->save();

        return redirect()->route('admin.suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' updated successfully. Changes are live for mobile app!");
    }
}
