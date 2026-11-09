<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Document;
use App\Models\IndustryCategory;
use App\Models\Supplier;
use App\Models\SupplierReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SupplierAdminController extends Controller
{
    /**
     * Display a listing of suppliers for admin.
     */
    public function index(Request $request): View
    {
        $query = Supplier::query()->withCount('documents')->latest();

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('industry_category', 'like', "%{$keyword}%")
                    ->orWhere('country', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhere('whatsapp', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $suppliers = $query->paginate(15);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create(): View
    {
        $categories = IndustryCategory::orderBy('name')->get();

        return view('admin.suppliers.create', compact('categories'));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'about' => ['nullable', 'string'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'industry_category' => ['required', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'company_size' => ['nullable', 'string', 'max:100'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'moq' => ['nullable', 'string', 'max:100'],
            'production_capacity' => ['nullable', 'string', 'max:100'],
            'avg_lead_time' => ['nullable', 'string', 'max:100'],
            'carbon_footprint_report' => ['nullable', 'string', 'max:100'],
            'is_verified' => ['nullable', 'boolean'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'logo_file' => ['nullable', 'image', 'max:2048'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug ?: 'supplier-'.Str::random(6);
        $counter = 1;
        while (Supplier::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $logoPath = null;
        if ($request->hasFile('logo_file')) {
            $logoPath = $request->file('logo_file')->store('suppliers', 'public');
        } elseif (! empty($validated['logo_url'])) {
            $logoPath = $validated['logo_url'];
        }

        $supplier = Supplier::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'about' => $validated['about'] ?? null,
            'country' => $validated['country'],
            'city' => $validated['city'] ?? null,
            'industry_category' => $validated['industry_category'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'email' => $validated['email'] ?? null,
            'website' => $validated['website'] ?? null,
            'company_size' => $validated['company_size'] ?? null,
            'founded_year' => $validated['founded_year'] ?? null,
            'moq' => $validated['moq'] ?? null,
            'production_capacity' => $validated['production_capacity'] ?? null,
            'avg_lead_time' => $validated['avg_lead_time'] ?? null,
            'carbon_footprint_report' => $validated['carbon_footprint_report'] ?? null,
            'is_verified' => $request->has('is_verified'),
            'logo' => $logoPath,
        ]);

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', "Pemasok '{$supplier->name}' berhasil ditambahkan. Anda dapat menambahkan dokumen profil sekarang.");
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier): View
    {
        $supplier->load(['documents', 'tags', 'certifications.certification', 'reviews.user']);
        $categories = IndustryCategory::orderBy('name')->get();
        $certifications = Certification::orderBy('category')->orderBy('name')->get();

        return view('admin.suppliers.edit', compact('supplier', 'categories', 'certifications'));
    }

    /**
     * Synchronize certifications for the supplier.
     */
    public function syncCertifications(Request $request, Supplier $supplier): RedirectResponse
    {
        $submitted = $request->input('certifications', []);

        // Delete existing certifications and recreate from submitted active ones
        $supplier->certifications()->delete();

        foreach ($submitted as $certId => $data) {
            if (! empty($data['active'])) {
                $status = in_array($data['status'] ?? '', ['verified', 'partial', 'not_verified']) ? $data['status'] : 'verified';
                $supplier->certifications()->create([
                    'certification_id' => (int) $certId,
                    'status' => $status,
                    'is_premium_field' => ! empty($data['is_premium_field']),
                ]);
            }
        }

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', 'Daftar sertifikasi standar pemasok berhasil diperbarui.');
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'about' => ['nullable', 'string'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'industry_category' => ['required', 'string', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'company_size' => ['nullable', 'string', 'max:100'],
            'founded_year' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'moq' => ['nullable', 'string', 'max:100'],
            'production_capacity' => ['nullable', 'string', 'max:100'],
            'avg_lead_time' => ['nullable', 'string', 'max:100'],
            'carbon_footprint_report' => ['nullable', 'string', 'max:100'],
            'is_verified' => ['nullable', 'boolean'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'logo_file' => ['nullable', 'image', 'max:2048'],
        ]);

        $supplier->name = $validated['name'];
        $supplier->tagline = $validated['tagline'] ?? $supplier->tagline;
        $supplier->description = $validated['description'] ?? $supplier->description;
        $supplier->about = $validated['about'] ?? $supplier->about;
        $supplier->country = $validated['country'];
        $supplier->city = $validated['city'] ?? $supplier->city;
        $supplier->industry_category = $validated['industry_category'];
        $supplier->whatsapp = $validated['whatsapp'] ?? $supplier->whatsapp;
        $supplier->email = $validated['email'] ?? $supplier->email;
        $supplier->website = $validated['website'] ?? $supplier->website;
        $supplier->company_size = $validated['company_size'] ?? $supplier->company_size;
        $supplier->founded_year = $validated['founded_year'] ?? $supplier->founded_year;
        $supplier->moq = $validated['moq'] ?? $supplier->moq;
        $supplier->production_capacity = $validated['production_capacity'] ?? $supplier->production_capacity;
        $supplier->avg_lead_time = $validated['avg_lead_time'] ?? $supplier->avg_lead_time;
        $supplier->carbon_footprint_report = $validated['carbon_footprint_report'] ?? $supplier->carbon_footprint_report;
        $supplier->is_verified = $request->has('is_verified');

        // Handle logo file upload or URL
        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('suppliers', 'public');
            $supplier->logo = $path;
        } elseif (! empty($validated['logo_url'])) {
            $supplier->logo = $validated['logo_url'];
        }

        $supplier->save();

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', "Data pemasok '{$supplier->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $name = $supplier->name;

        // Delete associated document files
        foreach ($supplier->documents as $doc) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }

        $supplier->documents()->delete();
        $supplier->tags()->delete();
        $supplier->certifications()->delete();
        $supplier->savedBy()->delete();
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
            ->with('success', "Pemasok '{$name}' dan seluruh berkas terkait berhasil dihapus.");
    }

    /**
     * Store a document for the supplier.
     */
    public function storeDocument(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:50'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:15360'],
            'is_premium' => ['nullable', 'boolean'],
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $supplier->documents()->create([
            'title' => $validated['title'],
            'type' => strtoupper($validated['type'] ?? $request->file('file')->getClientOriginalExtension()),
            'file_path' => $path,
            'is_premium' => $request->has('is_premium'),
        ]);

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', "Dokumen '{$validated['title']}' berhasil diunggah.");
    }

    /**
     * Remove a document from the supplier.
     */
    public function destroyDocument(Supplier $supplier, Document $document): RedirectResponse
    {
        if ($document->supplier_id !== $supplier->id) {
            abort(403, 'Akses tidak sah ke dokumen pemasok.');
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $title = $document->title;
        $document->delete();

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', "Dokumen '{$title}' berhasil dihapus.");
    }

    /**
     * Provide an official vendor response to a review.
     */
    public function replyReview(Request $request, Supplier $supplier, SupplierReview $review): RedirectResponse
    {
        if ($review->supplier_id !== $supplier->id) {
            abort(404);
        }

        $validated = $request->validate([
            'supplier_reply' => ['required', 'string', 'max:3000'],
        ]);

        $review->supplier_reply = $validated['supplier_reply'];
        $review->replied_at = now();
        $review->save();

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', 'Tanggapan resmi pemasok berhasil dipublikasikan.');
    }

    /**
     * Delete a review as an administrator.
     */
    public function destroyReview(Supplier $supplier, SupplierReview $review): RedirectResponse
    {
        if ($review->supplier_id !== $supplier->id) {
            abort(404);
        }

        $review->delete();

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', 'Ulasan pembeli berhasil dihapus.');
    }
}
