<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComparisonController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Agrim Directory / SupplyChain Pro (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public Auth Routes (Rate-limited against brute-force & credential stuffing)
    Route::middleware('throttle:api-auth')->group(function () {
        Route::post('/auth/register', [AuthController::class, 'register'])->name('api.auth.register');
        Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');
        Route::post('/auth/google', [AuthController::class, 'google'])->name('api.auth.google');
    });

    // Public Directory Routes
    Route::get('/dashboard/home', [DashboardController::class, 'home'])->name('api.dashboard.home');
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('api.suppliers.index');
    Route::match(['get', 'post'], '/suppliers/qr-resolve', [SupplierController::class, 'qrResolve'])->name('api.suppliers.qr-resolve');
    Route::post('/suppliers/compare', [ComparisonController::class, 'compare'])->name('api.compare');
    Route::get('/suppliers/{idOrSlug}', [SupplierController::class, 'show'])->name('api.suppliers.show');

    // Documents (public/freemium handled inside controller)
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('api.documents.download');

    // Protected Routes (Sanctum Token required)
    Route::middleware('auth:sanctum')->group(function () {
        Route::match(['put', 'patch', 'post'], '/suppliers/{id}', [SupplierController::class, 'update'])
            ->middleware('can:access-admin')
            ->name('api.suppliers.update');

        Route::get('/auth/user', [AuthController::class, 'user'])->name('api.auth.user');
        Route::get('/auth/me', [AuthController::class, 'user'])->name('api.auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

        // Collection / Saved Suppliers
        Route::post('/suppliers/{id}/toggle-save', [SupplierController::class, 'toggleSave'])->name('api.suppliers.toggle-save');
        Route::get('/collection/saved', [SupplierController::class, 'savedSuppliers'])->name('api.suppliers.saved');

        // Comparison Export PDF (Premium)
        Route::post('/suppliers/compare/download-pdf', [ComparisonController::class, 'downloadPdf'])->name('api.compare.pdf');

        // Google Play Billing In-App Purchase Verification
        Route::post('/billing/verify-play-purchase', [SubscriptionController::class, 'verifyPlayBilling'])->name('api.subscription.verify-play-billing');
    });
});

// Fallback legacy endpoint
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
