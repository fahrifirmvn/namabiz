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

        return view('suppliers.show', compact('supplier', 'isSaved'));
    }
}
