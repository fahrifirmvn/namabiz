<?php

use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\CertificationAdminController;
use App\Http\Controllers\Admin\SupplierAdminController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProcurementCollectionController;
use App\Http\Controllers\SavedSupplierController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierReviewController;
use App\Models\IndustryCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:access-admin'])
    ->group(function () {
        Route::resource('suppliers', SupplierAdminController::class)->except(['show']);
        Route::post('suppliers/{supplier}/documents', [SupplierAdminController::class, 'storeDocument'])->name('suppliers.documents.store');
        Route::post('suppliers/{supplier}/documents/bulk', [SupplierAdminController::class, 'storeDocuments'])->name('suppliers.documents.bulk');
        Route::patch('suppliers/{supplier}/verify', [SupplierAdminController::class, 'toggleVerification'])->name('suppliers.verify');
        Route::delete('suppliers/{supplier}/documents/{document}', [SupplierAdminController::class, 'destroyDocument'])->name('suppliers.documents.destroy');
        Route::post('suppliers/{supplier}/certifications', [SupplierAdminController::class, 'syncCertifications'])->name('suppliers.certifications.sync');
        Route::post('suppliers/{supplier}/reviews/{review}/reply', [SupplierAdminController::class, 'replyReview'])->name('suppliers.reviews.reply');
        Route::delete('suppliers/{supplier}/reviews/{review}', [SupplierAdminController::class, 'destroyReview'])->name('suppliers.reviews.destroy');

        Route::resource('categories', CategoryAdminController::class)->except(['show']);
        Route::resource('certifications', CertificationAdminController::class)->except(['show']);
    });

Route::get('/', function () {
    $categories = IndustryCategory::withCount('suppliers')
        ->orderByDesc('suppliers_count')
        ->orderBy('name')
        ->limit(8)
        ->get();

    return view('landing', compact('categories'));
})->name('home');

Route::get('/discover', [DiscoverController::class, 'index'])->name('discover.index');
Route::get('/suppliers/{supplier:slug}', [SupplierController::class, 'show'])->name('suppliers.show');
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

Route::prefix('analytics')->group(function () {
    Route::get('/compare', [ComparisonController::class, 'index'])->name('compare.index');
    Route::get('/compare/download-pdf', [ComparisonController::class, 'downloadPdf'])
        ->middleware('ensure.premium')
        ->name('compare.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/saved-suppliers', [SavedSupplierController::class, 'index'])->name('saved.index');
    Route::post('/suppliers/{supplier}/save', [SavedSupplierController::class, 'toggle'])->name('suppliers.save');
    Route::post('/saved-suppliers/{savedSupplier}/move', [SavedSupplierController::class, 'move'])->name('saved.move');
    Route::delete('/saved-suppliers/{savedSupplier}', [SavedSupplierController::class, 'destroy'])->name('saved.destroy');

    Route::post('/procurement-collections', [ProcurementCollectionController::class, 'store'])->name('collections.store');
    Route::put('/procurement-collections/{collection}', [ProcurementCollectionController::class, 'update'])->name('collections.update');
    Route::delete('/procurement-collections/{collection}', [ProcurementCollectionController::class, 'destroy'])->name('collections.destroy');

    Route::post('/suppliers/{supplier:slug}/reviews', [SupplierReviewController::class, 'store'])->name('suppliers.reviews.store');
    Route::delete('/suppliers/{supplier:slug}/reviews/{review}', [SupplierReviewController::class, 'destroy'])->name('suppliers.reviews.destroy');

    Route::get('/upgrade', [SubscriptionController::class, 'show'])->name('upgrade');
    Route::post('/upgrade/checkout', [SubscriptionController::class, 'checkout'])->name('upgrade.checkout');

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

Route::get('dashboard', function () {
    $user = auth()->user();
    $savedCount = $user->savedSuppliers()->count();
    $savedSuppliers = $user->savedSuppliers()->with('supplier.tags')->get()->pluck('supplier')->filter();
    if ($savedSuppliers->isEmpty()) {
        $savedSuppliers = Supplier::with('tags')->limit(4)->get();
    }
    $recentlyViewed = Supplier::with('tags')->inRandomOrder()->limit(3)->get();
    $recommended = Supplier::with('tags')->latest()->limit(3)->get();
    $preferredCategories = IndustryCategory::withCount('suppliers')
        ->orderByDesc('suppliers_count')
        ->limit(3)
        ->get();

    return view('dashboard', compact('savedSuppliers', 'savedCount', 'recentlyViewed', 'recommended', 'preferredCategories'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
