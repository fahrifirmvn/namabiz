<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('access-admin') ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
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
            'remove_logo' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'string', 'max:1500'],
            'certifications' => ['nullable', 'array'],
            'documents' => ['nullable', 'array', 'max:20'],
            'documents.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:15360'],
            'doc_titles' => ['nullable', 'array'],
            'doc_titles.*' => ['nullable', 'string', 'max:255'],
            'doc_premium' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'documents.*.mimes' => 'Dokumen harus berformat PDF, DOC, DOCX, XLS, atau XLSX.',
            'documents.*.max' => 'Ukuran setiap dokumen maksimal 15 MB.',
            'documents.max' => 'Maksimal 20 dokumen sekali unggah.',
        ];
    }
}
