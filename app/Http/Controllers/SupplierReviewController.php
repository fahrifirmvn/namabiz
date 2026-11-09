<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierReviewController extends Controller
{
    /**
     * Store or update a supplier review from the authenticated user.
     */
    public function store(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'quality_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'delivery_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'service_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['required', 'string', 'max:255'],
            'comment' => ['required', 'string', 'max:3000'],
            'reviewer_company' => ['nullable', 'string', 'max:255'],
            'reviewer_role' => ['nullable', 'string', 'max:150'],
        ]);

        $user = Auth::user();

        SupplierReview::updateOrCreate(
            [
                'supplier_id' => $supplier->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => $validated['rating'],
                'quality_rating' => $validated['quality_rating'],
                'delivery_rating' => $validated['delivery_rating'],
                'service_rating' => $validated['service_rating'],
                'title' => $validated['title'],
                'comment' => $validated['comment'],
                'reviewer_company' => $validated['reviewer_company'] ?? null,
                'reviewer_role' => $validated['reviewer_role'] ?? null,
                'is_verified_buyer' => (bool) $user->isPremium(),
            ]
        );

        return redirect()->route('suppliers.show', ['supplier' => $supplier->slug, 'tab' => 'ulasan'])
            ->with('success', 'Ulasan dan evaluasi rekanan berhasil disimpan.');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Supplier $supplier, SupplierReview $review): RedirectResponse
    {
        if ($review->supplier_id !== $supplier->id) {
            abort(404);
        }

        if ($review->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403, 'Akses tidak sah untuk menghapus ulasan ini.');
        }

        $review->delete();

        return redirect()->route('suppliers.show', ['supplier' => $supplier->slug, 'tab' => 'ulasan'])
            ->with('success', 'Ulasan Anda berhasil dihapus.');
    }
}
