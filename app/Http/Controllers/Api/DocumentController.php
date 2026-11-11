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
            $scheme = strtolower((string) parse_url($document->external_url, PHP_URL_SCHEME));
            if (! in_array($scheme, ['http', 'https'], true) || ! filter_var($document->external_url, FILTER_VALIDATE_URL)) {
                return ApiResponse::error('Document URL is invalid.', 404);
            }

            return redirect()->away($document->external_url);
        }

        $filePath = (string) $document->file_path;
        if ($filePath !== '' && ! str_contains($filePath, '..')) {
            $safeTitle = trim((string) preg_replace('/[^\w\s\.\-\(\)]/u', '', (string) $document->title)) ?: 'document';
            $headers = ['X-Content-Type-Options' => 'nosniff'];

            if (Storage::disk('local')->exists($filePath)) {
                return Storage::disk('local')->download($filePath, $safeTitle, $headers);
            }

            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath, $safeTitle, $headers);
            }
        }

        return ApiResponse::error('Document file not available on the server.', 404);
    }
}
