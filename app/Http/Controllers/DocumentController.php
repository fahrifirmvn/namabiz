<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Support\PremiumAccess;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Server-side gate for document access, independent of anything hidden in the Blade layer.
     */
    public function download(Document $document)
    {
        if ($document->is_premium && ! PremiumAccess::isPremium(auth()->user())) {
            abort(403, 'This document requires a Premium subscription.');
        }

        if ($document->external_url) {
            $scheme = strtolower((string) parse_url($document->external_url, PHP_URL_SCHEME));
            if (! in_array($scheme, ['http', 'https'], true) || ! filter_var($document->external_url, FILTER_VALIDATE_URL)) {
                abort(404, 'Document URL is invalid.');
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

        abort(404, 'Document file not available.');
    }
}
