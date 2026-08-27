<?php

use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SavedSupplierController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [DiscoverController::class, 'index'])->name('discover.index');
Route::get('/suppliers/{supplier:slug}', [SupplierController::class, 'show'])->name('suppliers.show');
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

Route::prefix('analytics')->group(function () {
    Route::get('/compare', [ComparisonController::class, 'index'])->name('compare.index');
    Route::get('/compare/download-pdf', [ComparisonController::class, 'downloadPdf'])
        ->middleware('ensure.premium')
        ->name('compare.download');
});

Route::middleware('auth')->group(function () {
    Route::post('/suppliers/{supplier}/save', [SavedSupplierController::class, 'toggle'])->name('suppliers.save');
    Route::get('/upgrade', [SubscriptionController::class, 'show'])->name('upgrade');
    Route::post('/upgrade/checkout', [SubscriptionController::class, 'checkout'])->name('upgrade.checkout');

    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
