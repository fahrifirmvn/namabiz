<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function show(Supplier $supplier): View
    {
        $supplier->load(['tags', 'documents', 'certifications.certification']);

        $reviewStats = $supplier->reviewStats();
        $supplier->setRelation('reviews', $supplier->reviews()->with('user')->latest()->limit(20)->get());
        $supplier->setAttribute('reviews_count', $reviewStats['count']);
        $supplier->setAttribute('reviews_avg_rating', $reviewStats['average']);

        $isSaved = auth()->check()
            && $supplier->savedBy()->where('user_id', auth()->id())->exists();

        $userReview = auth()->check()
            ? $supplier->reviews()->where('user_id', auth()->id())->first()
            : null;

        $similarSuppliers = Supplier::with(['tags', 'certifications.certification'])
            ->where('industry_category', $supplier->industry_category)
            ->where('id', '!=', $supplier->id)
            ->limit(3)
            ->get();

        return view('suppliers.show', compact('supplier', 'isSaved', 'similarSuppliers', 'userReview', 'reviewStats'));
    }
}
