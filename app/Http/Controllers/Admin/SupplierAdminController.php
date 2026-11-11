<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Certification;
use App\Models\Document;
use App\Models\IndustryCategory;
use App\Models\Supplier;
use App\Models\SupplierReview;
use App\Observers\CatalogCacheObserver;
use App\Support\SqlLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SupplierAdminController extends Controller
{
    /**
     * Columns that are copied straight from the validated request onto the supplier.
     *
     * @var array<int, string>
     */
    private const PROFILE_FIELDS = [
        'name', 'tagline', 'description', 'about', 'country', 'city', 'industry_category',
        'whatsapp', 'email', 'website', 'company_size', 'founded_year', 'moq',
        'production_capacity', 'avg_lead_time', 'carbon_footprint_report',
    ];

    /**
     * Display a listing of suppliers for admin.
     */
    public function index(Request $request): View
    {
        $sort = $request->input('sort', 'newest');

        $suppliers = Supplier::query()
            ->withCount(['documents', 'tags', 'certifications'])
            ->when($request->filled('q') && is_string($request->input('q')), function ($query) use ($request) {
                $pattern = SqlLike::pattern($request->input('q'));
                if ($pattern !== null) {
                    $query->where(function ($q) use ($pattern) {
                        $q->where('name', 'like', $pattern)
                            ->orWhere('industry_category', 'like', $pattern)
                            ->orWhere('country', 'like', $pattern)
                            ->orWhere('city', 'like', $pattern)
                            ->orWhere('whatsapp', 'like', $pattern)
                            ->orWhere('email', 'like', $pattern);
                    });
                }
            })
            ->when($request->filled('category'), fn ($query) => $query->where('industry_category', $request->input('category')))
            ->when($request->input('status') === 'verified', fn ($query) => $query->where('is_verified', true))
            ->when($request->input('status') === 'standard', fn ($query) => $query->where('is_verified', false))
            ->when(
                $sort === 'name',
                fn ($query) => $query->orderBy('name'),
                fn ($query) => $sort === 'oldest' ? $query->oldest() : $query->latest()
            )
            ->paginate(15)
            ->withQueryString();

        $categories = IndustryCategory::orderBy('name')->pluck('name');

        return view('admin.suppliers.index', compact('suppliers', 'categories'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create(): View
    {
        $categories = IndustryCategory::orderBy('name')->get();
        $certifications = Certification::orderBy('category')->orderBy('name')->get();
        $supplier = new Supplier(['country' => 'Indonesia']);

        return view('admin.suppliers.create', compact('categories', 'certifications', 'supplier'));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(SupplierRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug ?: 'supplier-'.Str::random(6);
        $counter = 1;
        while (Supplier::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $supplier = new Supplier(['slug' => $slug]);
        $this->fillProfile($supplier, $request);
        $supplier->save();

        $this->syncTags($supplier, $request->input('tags'));
        $this->syncCertificationInput($supplier, (array) $request->input('certifications', []));
        $uploaded = $this->storeUploadedDocuments($supplier, $request);

        $message = "Pemasok '{$supplier->name}' berhasil ditambahkan";
        if ($uploaded > 0) {
            $message .= " beserta {$uploaded} dokumen";
        }

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', $message.'.');
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier): View
    {
        $supplier->load(['documents', 'tags', 'certifications.certification']);
        $supplier->loadCount('reviews')->loadAvg('reviews', 'rating');
        $supplier->setRelation('reviews', $supplier->reviews()->with('user')->latest()->limit(50)->get());

        $categories = IndustryCategory::orderBy('name')->get();
        $certifications = Certification::orderBy('category')->orderBy('name')->get();
        $completeness = $supplier->profileCompleteness();

        return view('admin.suppliers.edit', compact('supplier', 'categories', 'certifications', 'completeness'));
    }

    /**
     * Synchronize certifications for the supplier.
     */
    public function syncCertifications(Request $request, Supplier $supplier): RedirectResponse
    {
        $this->syncCertificationInput($supplier, (array) $request->input('certifications', []));

        CatalogCacheObserver::flush();

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', 'Daftar sertifikasi standar pemasok berhasil diperbarui.')
            ->with('tab', 'sertifikasi');
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $this->fillProfile($supplier, $request);
        $supplier->save();

        if ($request->has('tags')) {
            $this->syncTags($supplier, $request->input('tags'));
        }

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', "Data pemasok '{$supplier->name}' berhasil diperbarui.");
    }

    /**
     * Quickly flip the verified badge from the supplier list.
     */
    public function toggleVerification(Supplier $supplier): RedirectResponse
    {
        $supplier->is_verified = ! $supplier->is_verified;
        $supplier->save();

        $state = $supplier->is_verified ? 'diverifikasi' : 'dicabut verifikasinya';

        return back()->with('success', "Pemasok '{$supplier->name}' berhasil {$state}.");
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
            ->with('success', "Dokumen '{$validated['title']}' berhasil diunggah.")
            ->with('tab', 'dokumen');
    }

    /**
     * Upload several documents at once.
     */
    public function storeDocuments(Request $request, Supplier $supplier): RedirectResponse
    {
        $request->validate([
            'documents' => ['required', 'array', 'min:1', 'max:20'],
            'documents.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:15360'],
            'doc_titles.*' => ['nullable', 'string', 'max:255'],
        ], [
            'documents.*.mimes' => 'Dokumen harus berformat PDF, DOC, DOCX, XLS, atau XLSX.',
            'documents.*.max' => 'Ukuran setiap dokumen maksimal 15 MB.',
        ]);

        $uploaded = $this->storeUploadedDocuments($supplier, $request);

        return redirect()->route('admin.suppliers.edit', $supplier)
            ->with('success', "{$uploaded} dokumen berhasil diunggah.")
            ->with('tab', 'dokumen');
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
            ->with('success', "Dokumen '{$title}' berhasil dihapus.")
            ->with('tab', 'dokumen');
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
            ->with('success', 'Tanggapan resmi pemasok berhasil dipublikasikan.')
            ->with('tab', 'ulasan');
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
            ->with('success', 'Ulasan pembeli berhasil dihapus.')
            ->with('tab', 'ulasan');
    }

    /**
     * Copy validated profile data and the logo onto the supplier without saving it.
     * Fields that were submitted blank are cleared; fields that were not submitted are untouched.
     */
    private function fillProfile(Supplier $supplier, SupplierRequest $request): void
    {
        $supplier->fill(Arr::only($request->validated(), self::PROFILE_FIELDS));
        $supplier->about ??= '';
        $supplier->is_verified = $request->boolean('is_verified');

        $previousLogo = $supplier->logo;

        if ($request->hasFile('logo_file')) {
            $supplier->logo = $request->file('logo_file')->store('suppliers', 'public');
        } elseif ($request->filled('logo_url')) {
            $supplier->logo = $request->input('logo_url');
        } elseif ($request->boolean('remove_logo')) {
            $supplier->logo = null;
        }

        if ($previousLogo && $previousLogo !== $supplier->logo && ! str_starts_with($previousLogo, 'http')) {
            Storage::disk('public')->delete($previousLogo);
        }
    }

    /**
     * Replace the supplier's tags using a comma or newline separated list.
     */
    private function syncTags(Supplier $supplier, ?string $raw): void
    {
        $names = collect(preg_split('/[,\n]+/', (string) $raw))
            ->map(fn (string $name): string => Str::limit(trim($name), 50, ''))
            ->filter()
            ->unique(fn (string $name): string => mb_strtolower($name))
            ->take(20)
            ->values();

        $supplier->tags()->delete();
        $supplier->tags()->createMany($names->map(fn (string $name): array => ['tag_name' => $name])->all());
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $submitted
     */
    private function syncCertificationInput(Supplier $supplier, array $submitted): void
    {
        $supplier->certifications()->delete();

        foreach ($submitted as $certId => $data) {
            if (empty($data['active'])) {
                continue;
            }

            $status = in_array($data['status'] ?? '', ['verified', 'partial', 'not_verified'], true) ? $data['status'] : 'verified';
            $supplier->certifications()->create([
                'certification_id' => (int) $certId,
                'status' => $status,
                'is_premium_field' => ! empty($data['is_premium_field']),
            ]);
        }
    }

    /**
     * Store every uploaded file in `documents[]`, deriving title and type when not provided.
     */
    private function storeUploadedDocuments(Supplier $supplier, Request $request): int
    {
        $files = $request->file('documents', []);
        $titles = (array) $request->input('doc_titles', []);
        $premium = (array) $request->input('doc_premium', []);

        foreach ($files as $index => $file) {
            $supplier->documents()->create([
                'title' => filled($titles[$index] ?? null)
                    ? $titles[$index]
                    : Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))->replace(['_', '-'], ' ')->title()->toString(),
                'type' => strtoupper($file->getClientOriginalExtension()),
                'file_path' => $file->store('documents', 'public'),
                'is_premium' => isset($premium[$index]),
            ]);
        }

        return count($files);
    }
}
