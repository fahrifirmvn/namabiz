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
            return redirect()->away($document->external_url);
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $document->title);
        }

        abort(404, 'Document file not available.');
    }
}
