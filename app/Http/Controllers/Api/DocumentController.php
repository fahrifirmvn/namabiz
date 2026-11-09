<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Support\ApiResponse;
use App\Support\PremiumAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    /**
     * Download or stream the specified supplier brochure/document.
     */
    public function download(Request $request, int $id): Response
    {
        $document = Document::find($id);

        if (! $document) {
            return ApiResponse::error('Document not found.', 404);
        }

        $user = $request->user('sanctum') ?? $request->user();

        if ($document->is_premium && ! PremiumAccess::isPremium($user)) {
            return ApiResponse::error(
                'This document requires an active Premium subscription.',
                403,
                ['paywall' => true]
            );
        }

        if ($document->external_url) {
            return redirect()->away($document->external_url);
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $document->title);
        }

        return ApiResponse::error('Document file not available on the server.', 404);
    }
}
