<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function show(Supplier $supplier): View
    {
        $supplier->load([
            'tags',
            'documents',
            'certifications.certification',
            'reviews' => fn ($q) => $q->latest()->with('user'),
        ]);
        $supplier->loadCount('reviews');

        $isSaved = auth()->check()
            && $supplier->savedBy()->where('user_id', auth()->id())->exists();

        $userReview = auth()->check()
            ? $supplier->reviews->firstWhere('user_id', auth()->id())
            : null;

        $similarSuppliers = Supplier::with(['tags', 'certifications.certification'])
            ->where('industry_category', $supplier->industry_category)
            ->where('id', '!=', $supplier->id)
            ->limit(3)
            ->get();

        return view('suppliers.show', compact('supplier', 'isSaved', 'similarSuppliers', 'userReview'));
    }
}
