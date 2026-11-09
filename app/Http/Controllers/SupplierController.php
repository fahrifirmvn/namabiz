<?php

namespace App\Http\Controllers;

use App\Models\Supplier;

class SupplierController extends Controller
{
    public function show(Supplier $supplier)
    {
        $supplier->load(['tags', 'documents', 'certifications.certification']);

        $isSaved = auth()->check()
            && $supplier->savedBy()->where('user_id', auth()->id())->exists();

        $similarSuppliers = Supplier::with(['tags', 'certifications.certification'])
            ->where('industry_category', $supplier->industry_category)
            ->where('id', '!=', $supplier->id)
            ->limit(3)
            ->get();

        return view('suppliers.show', compact('supplier', 'isSaved', 'similarSuppliers'));
    }
}
