# Development Prompt: SupplyChain Pro — B2B Supplier Platform (Laravel)

> Prompt ini ditulis untuk diberikan ke AI coding assistant (Claude Code, Cursor, dll) atau tim developer, sebagai spesifikasi lengkap membangun platform B2B supplier discovery dengan model **Freemium** menggunakan **Laravel**.

---

## 1. Ringkasan Proyek

Bangun aplikasi web **"SupplyChain Pro"** menggunakan **Laravel 11**, yaitu platform pencarian & perbandingan supplier industri (manufaktur, packaging, raw material). Model bisnis **Freemium**: user gratis bisa browsing & lihat data dasar; data operasional/teknis mendalam terkunci di balik langganan **Premium**.

**Fitur inti:**
1. Autentikasi user (free & premium tier)
2. Pencarian & filter supplier
3. Perbandingan supplier side-by-side
4. Halaman profil detail supplier
5. Sistem paywall dinamis (locked field berdasarkan status langganan)
6. Modal upgrade & flow ke halaman billing

---

## 2. Tech Stack

| Layer | Teknologi |
|---|---|
| Backend Framework | Laravel 11 (PHP 8.2+) |
| Frontend Templating | Blade + Blade Components |
| Interaktivitas | Livewire 3 (untuk filter real-time, modal, tabel compare) + Alpine.js (untuk micro-interaction ringan) |
| Styling | Tailwind CSS 3 (via Laravel Vite) |
| Database | MySQL / PostgreSQL |
| Auth | Laravel Breeze / Fortify (sesuaikan dengan kebutuhan session-based auth) |
| Authorization | Laravel Gate & Policy untuk logic premium/free |
| Icon | Blade Icons (heroicons) |
| Queue (opsional) | Untuk generate PDF comparison secara async |
| PDF Generator | `barryvdh/laravel-dompdf` atau `spatie/laravel-pdf` untuk fitur "Download Comparison (PDF)" |

---

## 3. Struktur Database (Migration)

### 3.1 Tabel `users` (extend default Laravel)
```php
Schema::table('users', function (Blueprint $table) {
    $table->enum('subscription_tier', ['free', 'premium'])->default('free');
    $table->timestamp('premium_expires_at')->nullable();
    $table->string('avatar')->nullable();
});
```

### 3.2 Tabel `suppliers`
```php
Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('logo')->nullable();
    $table->string('tagline')->nullable();
    $table->text('description')->nullable();
    $table->string('country');
    $table->string('city')->nullable();
    $table->string('industry_category'); // e.g. "Packaging Manufacturer"
    $table->boolean('is_verified')->default(false);
    $table->text('about'); // free, selalu terbuka

    // FREE fields
    $table->string('website')->nullable();

    // PREMIUM fields (tetap disimpan di DB, akses dikontrol di layer aplikasi, bukan di DB)
    $table->year('founded_year')->nullable();
    $table->string('company_size')->nullable(); // e.g. "500-1000 employees"
    $table->string('production_capacity')->nullable(); // e.g. "2M units/mo"
    $table->string('moq')->nullable(); // Minimum Order Quantity
    $table->string('avg_lead_time')->nullable();
    $table->string('carbon_footprint_report')->nullable();

    $table->timestamps();
});
```

### 3.3 Tabel `supplier_tags` (kapabilitas/capability tags)
```php
Schema::create('supplier_tags', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
    $table->string('tag_name'); // e.g. "PET Bottles", "Corrugated Boxes"
});
```

### 3.4 Tabel `certifications` & pivot `supplier_certifications`
```php
Schema::create('certifications', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // ISO 9001, ISO 14001, FSSC 22000, HACCP
    $table->string('category'); // Quality Management, Environmental, Food Safety
});

Schema::create('supplier_certifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
    $table->foreignId('certification_id')->constrained()->cascadeOnDelete();
    $table->enum('status', ['verified', 'not_verified', 'partial'])->default('verified');
    $table->boolean('is_premium_field')->default(true); // ISO 9001 = false (gratis), lainnya = true
});
```

### 3.5 Tabel `documents`
```php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
    $table->string('title'); // "Company Profile", "Product Catalog Q3 2024"
    $table->string('type'); // pdf, web_link
    $table->string('file_path')->nullable();
    $table->string('external_url')->nullable();
    $table->boolean('is_premium')->default(true);
    $table->timestamps();
});
```

### 3.6 Tabel `saved_suppliers` (bookmark) & `comparisons`
```php
Schema::create('saved_suppliers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
});

Schema::create('comparisons', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->json('supplier_ids'); // array id supplier yang dibandingkan
    $table->timestamps();
});
```

---

## 4. Authorization Logic (Inti Sistem Paywall)

Gunakan **Laravel Gate/Policy**, jangan hardcode `if ($user->tier == 'premium')` tersebar di banyak file. Buat satu sumber kebenaran:

### 4.1 `app/Policies/SupplierPolicy.php` atau Helper khusus
```php
// app/Support/PremiumAccess.php
class PremiumAccess
{
    public static function isPremium(?User $user): bool
    {
        if (!$user) return false;
        return $user->subscription_tier === 'premium'
            && (!$user->premium_expires_at || $user->premium_expires_at->isFuture());
    }
}
```

### 4.2 Gate Definition (`AppServiceProvider` atau `AuthServiceProvider`)
```php
Gate::define('view-premium-data', function (?User $user) {
    return PremiumAccess::isPremium($user);
});
```

### 4.3 Middleware untuk route yang butuh premium penuh (opsional, misal download PDF)
```php
// app/Http/Middleware/EnsurePremiumUser.php
public function handle($request, Closure $next)
{
    if (!PremiumAccess::isPremium($request->user())) {
        return redirect()->route('upgrade')->with('paywall_triggered', true);
    }
    return $next($request);
}
```

### 4.4 Blade Directive Custom (untuk kemudahan di view)
```php
// AppServiceProvider::boot()
Blade::if('premium', function () {
    return PremiumAccess::isPremium(auth()->user());
});
```
Pemakaian di Blade:
```blade
@premium
    <span class="font-bold">{{ $supplier->moq }} units</span>
@else
    <span class="text-gray-400 flex items-center gap-1">
        <x-heroicon-o-lock-closed class="w-3 h-3" /> Premium
    </span>
@endpremium
```

---

## 5. Struktur Route

```php
// routes/web.php

Route::get('/', [DiscoverController::class, 'index'])->name('discover.index');
Route::get('/suppliers/{supplier:slug}', [SupplierController::class, 'show'])->name('suppliers.show');

Route::prefix('analytics')->group(function () {
    Route::get('/compare', [ComparisonController::class, 'index'])->name('compare.index');
    Route::post('/compare/add', [ComparisonController::class, 'addSupplier'])->name('compare.add');
    Route::get('/compare/download-pdf', [ComparisonController::class, 'downloadPdf'])
        ->middleware('ensure.premium')
        ->name('compare.download');
});

Route::middleware('auth')->group(function () {
    Route::post('/suppliers/{supplier}/save', [SavedSupplierController::class, 'toggle'])->name('suppliers.save');
    Route::get('/upgrade', [SubscriptionController::class, 'show'])->name('upgrade');
    Route::post('/upgrade/checkout', [SubscriptionController::class, 'checkout'])->name('upgrade.checkout');
});
```

---

## 6. Struktur Blade Component & Livewire

```
resources/views/
├── layouts/
│   └── app.blade.php              # layout utama (navbar + footer)
├── components/
│   ├── navbar.blade.php
│   ├── footer.blade.php
│   ├── locked-field.blade.php     # komponen reusable inti paywall
│   ├── premium-badge.blade.php
│   ├── supplier-card.blade.php
│   ├── document-list-item.blade.php
│   └── premium-cta-banner.blade.php
├── livewire/
│   ├── discover-search.blade.php  # filter + hasil pencarian real-time
│   ├── comparison-table.blade.php # tabel compare dinamis, add/remove supplier
│   └── premium-modal.blade.php    # modal paywall
├── suppliers/
│   └── show.blade.php             # halaman profil supplier
└── analytics/
    └── compare.blade.php
```

### 6.1 Komponen Inti: `<x-locked-field>`
Ini komponen paling penting — dipakai di HAMPIR semua tempat data premium ditampilkan.

```php
// app/View/Components/LockedField.php
class LockedField extends Component
{
    public bool $isLocked;

    public function __construct(public string $value, ?bool $forceLocked = null)
    {
        $this->isLocked = $forceLocked ?? !PremiumAccess::isPremium(auth()->user());
    }

    public function render()
    {
        return view('components.locked-field');
    }
}
```

```blade
{{-- resources/views/components/locked-field.blade.php --}}
@if($isLocked)
    <span class="inline-flex items-center gap-1 text-gray-400 text-sm">
        <x-heroicon-o-lock-closed class="w-3.5 h-3.5" />
        Premium
    </span>
@else
    <span class="text-gray-900 font-medium">{{ $value }}</span>
@endif
```

Pemakaian:
```blade
<x-locked-field :value="$supplier->production_capacity" />
```

### 6.2 Livewire Component: Modal Paywall
```php
// app/Livewire/PremiumModal.php
class PremiumModal extends Component
{
    public bool $show = false;
    public string $documentTitle = '';

    #[On('open-premium-modal')]
    public function open(string $documentTitle)
    {
        $this->documentTitle = $documentTitle;
        $this->show = true;
    }

    public function close() { $this->show = false; }
}
```
Trigger dari tombol lain via Alpine/Livewire event: `wire:click="$dispatch('open-premium-modal', { documentTitle: 'Product Catalog' })"`

---

## 7. Halaman & Wireframe Detail

### 7.1 Layout Global (`layouts/app.blade.php`)
- **Navbar** (sticky top, `bg-white border-b`):
  - Logo "SupplyChain Pro" (font-bold text-xl, kiri)
  - Nav menu: `Discover` `Analytics` `Messages` — active state pakai `border-b-2 border-black font-semibold`
  - Search bar global (`<input>` dengan icon search, rounded-lg bg-gray-100)
  - Tombol `Upgrade` — HANYA tampil jika `!auth()->user()?->isPremium()`, styling `bg-black text-white rounded-lg px-4 py-2`
  - Icon bell (notifikasi) + icon gear (settings)
  - Avatar bulat (dropdown menu: Profile, Billing, Logout)
- **Footer** (`bg-gray-100 border-t`): copyright kiri, link kanan (Terms, Privacy, Contact Support)

### 7.2 Halaman Discover — Search Results
**Route:** `GET /` → `DiscoverController@index`, konten utama pakai Livewire `discover-search`

**Layout:**
- Sidebar kiri (25% width, `<aside>`):
  - "Filters" header + tombol "Clear All" (`wire:click="clearFilters"`)
  - Filter group `Location`: checkbox list (Indonesia, Malaysia, Vietnam) + link "Show More" expand
  - Filter group `Category`: checkbox list
  - Filter group `Certifications`: checkbox + counter jumlah hasil di kanan tiap item
  - Semua checkbox pakai `wire:model.live` agar hasil update real-time tanpa reload

- Konten kanan (75% width):
  - Judul dinamis: `Results for "{{ $query }}"` + subtext jumlah hasil
  - Dropdown sort (`Relevance`, `Newest`, `Company Size`)
  - Grid card 2 kolom (`grid grid-cols-2 gap-6`), loop `@foreach($suppliers as $supplier)` render `<x-supplier-card :supplier="$supplier" />`
  - Pagination Livewire bawaan (`{{ $suppliers->links() }}`)

**Component `<x-supplier-card>`:**
```blade
<div class="bg-white rounded-xl border p-6 relative">
    <button class="absolute top-4 right-4"><x-heroicon-o-bookmark class="w-5 h-5" /></button>
    <div class="flex items-start gap-4">
        <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center">
            @if($supplier->logo)<img src="{{ $supplier->logo }}" class="rounded-lg" />@else<x-heroicon-o-building-office class="w-6 h-6 text-gray-400" />@endif
        </div>
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-xl font-bold">{{ $supplier->name }}</h3>
                @if($supplier->is_verified)
                    <x-premium-badge type="verified" />
                @endif
            </div>
            <p class="text-gray-500">{{ $supplier->industry_category }}</p>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mt-4">
        @foreach($supplier->tags as $tag)
            <span class="text-xs bg-gray-100 rounded-full px-3 py-1">{{ $tag->tag_name }}</span>
        @endforeach
    </div>

    <div class="bg-gray-50 rounded-lg p-4 mt-4 text-sm space-y-2">
        <div class="flex items-center gap-2 text-gray-700">
            <x-heroicon-o-check class="w-4 h-4 text-green-500" /> Basic company profile
        </div>
        @php $freeDocsCount = $supplier->documents->where('is_premium', false)->count(); @endphp
        @php $premiumDocsCount = $supplier->documents->where('is_premium', true)->count(); @endphp
        <div class="flex items-center gap-2 text-gray-400">
            <x-heroicon-o-lock-closed class="w-4 h-4" /> {{ $premiumDocsCount }} additional documents
        </div>
    </div>

    <div class="flex gap-3 mt-4">
        <a href="{{ route('suppliers.show', $supplier->slug) }}" class="flex-1 bg-black text-white rounded-lg py-2.5 text-center font-medium">View Profile →</a>
        <a href="#" class="flex-1 border rounded-lg py-2.5 text-center font-medium">Contact</a>
    </div>
</div>
```

### 7.3 Halaman Analytics — Compare Suppliers
**Route:** `GET /analytics/compare` → Livewire `comparison-table`

**Layout:**
- Sidebar kiri: info supplier utama yang dipilih (logo, nama, tier) + menu tab (Overview/Inventory/Certifications/Contact) + tombol "Contact Supplier"
- Header konten: judul "Compare Suppliers" + deskripsi + tombol "Download Comparison (PDF)"
  - Jika free: tombol disabled, icon gembok, `wire:click` trigger modal premium
  - Jika premium: tombol aktif, `wire:click="downloadPdf"` icon download normal

- **Tabel Perbandingan** (`<table>` dengan `sticky` header kolom kiri):
  - Baris section header (background abu, uppercase, bold kecil): "BASIC INFORMATION", "OPERATIONAL METRICS 🔒" (tambahkan icon gembok di judul section jika section tsb premium), "COMPLIANCE & SUSTAINABILITY", "DOCUMENT AVAILABILITY"
  - Setiap baris metrik loop per supplier kolom:
    ```blade
    <td>
        @if($isPremiumField && !$isPremium)
            <x-locked-field :value="$value" :force-locked="true" />
        @else
            {{ $value }}
        @endif
    </td>
    ```
  - Kolom terakhir "Add Supplier" — input search autocomplete (Livewire) untuk menambah supplier ke tabel (maks 3 kolom)

- **Banner CTA** (hanya render `@unless(auth()->user()?->isPremium())`):
  ```blade
  <div class="bg-gray-900 text-white rounded-xl p-8 flex items-center justify-between mt-8">
      <div>
          <h3 class="text-xl font-bold flex items-center gap-2">
              <x-heroicon-o-shield-check class="w-5 h-5" /> Unlock complete supplier data
          </h3>
          <p class="text-gray-300 mt-1">Compare operational metrics, lead times, and download full PDF reports to make informed procurement decisions.</p>
      </div>
      <a href="{{ route('upgrade') }}" class="bg-white text-black rounded-lg px-6 py-3 font-medium whitespace-nowrap">Explore Premium</a>
  </div>
  ```

### 7.4 Halaman Supplier Profile
**Route:** `GET /suppliers/{supplier:slug}` → `SupplierController@show`

**Layout:**
- Breadcrumb: `Discover > {{ $supplier->industry_category }} > {{ $supplier->name }}`
- Header: nama besar + badge (VERIFIED / PREMIUM MEMBER tergantung status supplier) + lokasi + industri
- Sidebar kiri: logo, tagline, tombol "Contact Supplier" (+ "Save Profile" jika premium), menu tab
- **Grid 2 kolom utama:**
  - Kiri (lebar): card "About Supplier" — paragraf `$supplier->about` (SELALU terbuka, tidak pernah dikunci) + tag "Core Capabilities"
  - Kanan (sempit): card "Company Info" — list field, campur free (`website`, `country`, `industry`) dan premium (`founded_year`, `company_size`, `production_capacity`, `moq`) pakai `<x-locked-field>`
- **Grid 2 kolom bawah:**
  - Card "Certifications" — loop `$supplier->certifications`, jika `is_premium_field` dan user bukan premium → tampil pudar (`opacity-40`) + badge "🔒 PREMIUM" pengganti checkmark
  - Card "Documents" — loop `$supplier->documents`, tombol download normal utk gratis, tombol "🔒 Unlock" (`wire:click="$dispatch('open-premium-modal', {...})"`) untuk premium
- **Banner CTA full-width** (dark, dengan personalisasi nama supplier), sama pola dengan compare page, hanya render jika user bukan premium

**State Premium** — kondisional di Blade:
```blade
@if($supplier->is_verified)
    <x-premium-badge :type="auth()->user()?->isPremium() ? 'premium_member' : 'verified'" />
@endif
```
- Section "Operational Capabilities" jadi grid 2x2 dengan angka besar (`text-3xl font-bold`) — hanya render versi ini jika premium
- Sidebar kanan tambahan "Institutional Documents" (icon eye + download aktif semua) dan card "Headquarters" dengan foto — khusus premium view

### 7.5 Modal Premium (Livewire, global di layout)
```blade
{{-- dipanggil sekali di layouts/app.blade.php --}}
<livewire:premium-modal />
```
```blade
{{-- resources/views/livewire/premium-modal.blade.php --}}
<div x-data x-show="$wire.show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50" wire:click="close"></div>
    <div class="relative bg-white rounded-2xl p-8 w-full max-w-md text-center shadow-xl">
        <div class="w-16 h-16 bg-gray-100 rounded-xl mx-auto flex items-center justify-center mb-4">
            <x-heroicon-o-lock-closed class="w-7 h-7 text-gray-700" />
        </div>
        <h2 class="text-2xl font-bold">Premium Document</h2>
        <p class="text-gray-500 mt-2">This {{ $documentTitle }} is available to Premium members. Get access to the complete supplier document library and more.</p>
        <a href="{{ route('upgrade') }}" class="block bg-black text-white rounded-lg py-3 mt-6 font-medium">Unlock with Premium</a>
        <button wire:click="close" class="text-gray-500 mt-4 text-sm">Continue browsing</button>
    </div>
</div>
```

---

## 8. Design System (Tailwind Config)

### 8.1 Warna (`tailwind.config.js`)
```js
theme: {
  extend: {
    colors: {
      surface: '#F7F8FA',
      border: '#E5E7EB',
      'text-primary': '#111827',
      'text-secondary': '#6B7280',
      success: '#10B981',
    }
  }
}
```

### 8.2 Prinsip Visual
- Card: `bg-white rounded-xl border border-gray-200 p-6`, hover: `hover:shadow-md transition-shadow`
- Tombol primary: `bg-black text-white rounded-lg px-6 py-2.5 font-medium hover:bg-gray-800`
- Tombol secondary: `border border-gray-300 rounded-lg px-6 py-2.5 font-medium hover:bg-gray-50`
- Badge verified: `bg-blue-50 text-blue-700 rounded-full px-3 py-1 text-xs font-medium`
- Badge premium member: `bg-gray-900 text-white rounded-full px-3 py-1 text-xs font-medium`
- Font: Inter (import via Google Fonts atau bundled), `font-sans`

---

## 9. Prinsip UX Paywall (Wajib Diikuti Konsisten)

1. **Jangan sembunyikan struktur data** — label field tetap terlihat, hanya *value*-nya diganti indikator terkunci (icon gembok + teks "Premium"). Ini membangun *curiosity gap* tanpa terasa menipu user.
2. **Field gratis vs premium harus konsisten di seluruh sistem** — didefinisikan sekali di level database/model (`is_premium_field` pada certifications & documents, kolom tetap di suppliers tapi dikontrol Gate), BUKAN dihardcode berulang-ulang di tiap Blade view.
3. **Satu sumber kebenaran otorisasi** — semua pengecekan premium HARUS lewat `PremiumAccess::isPremium()` / Gate `view-premium-data` / Blade directive `@premium`. Jangan ada logic tier tersebar di controller berbeda-beda dengan cara berbeda.
4. **CTA berulang tapi tidak agresif** — banner upgrade muncul di posisi natural (bawah konten utama) di setiap halaman utama, ditambah tombol "Upgrade" persisten di navbar (hanya utk free user).
5. **Server-side enforcement, bukan cuma UI** — jangan hanya sembunyikan data di Blade; pastikan endpoint API/download (`compare.download`, akses file dokumen) benar-benar dicegat middleware `ensure.premium`, supaya user tidak bisa akses via curl/devtools.

---

## 10. Checklist Implementasi Bertahap

```
[ ] 1. Setup project Laravel + Breeze (auth) + Livewire + Tailwind
[ ] 2. Buat migration & model: User (extend), Supplier, SupplierTag, 
       Certification, SupplierCertification, Document, SavedSupplier, Comparison
[ ] 3. Buat factory & seeder dengan data dummy realistis (10-20 supplier)
[ ] 4. Implementasi PremiumAccess helper + Gate + Blade directive @premium
[ ] 5. Buat layout utama (navbar, footer) sebagai Blade component
[ ] 6. Buat komponen <x-locked-field> dan <x-premium-badge>
[ ] 7. Bangun halaman Discover (search + filter) dengan Livewire
[ ] 8. Bangun <x-supplier-card> component
[ ] 9. Bangun halaman Supplier Profile (free & premium state)
[ ] 10. Bangun halaman Compare Suppliers + tabel dinamis Livewire
[ ] 11. Implementasi Livewire Modal Premium + trigger dari berbagai tombol lock
[ ] 12. Implementasi middleware ensure.premium untuk route sensitif (download PDF)
[ ] 13. Halaman Upgrade/Billing (bisa dummy dulu / integrasi payment gateway)
[ ] 14. Styling responsif (mobile: sidebar filter jadi drawer, tabel compare scroll horizontal)
[ ] 15. Testing: pastikan user free TIDAK bisa akses data premium via direct URL/API call
```

---

## 11. Ringkasan Referensi Free vs Premium

| Elemen | Free User | Premium User |
|---|---|---|
| Badge profil supplier | "VERIFIED" | "PREMIUM MEMBER" |
| Basic Info (lokasi, tahun, ukuran perusahaan dasar) | Terbuka | Terbuka |
| Operational Metrics (Capacity, MOQ, Lead Time) | 🔒 `<x-locked-field>` | Angka penuh, bold besar |
| Sertifikasi lanjutan (ISO 14001, dst) | 🔒 Pudar (`opacity-40`) | ✓ Full color checkmark |
| Dokumen (catalog, datasheet) | Sebagian + tombol "Unlock" → modal | Semua + icon view/download aktif |
| Download Comparison PDF | Disabled, trigger modal | Aktif, `middleware('ensure.premium')` |
| Banner CTA "Explore Premium" | Selalu muncul | `@unless` — disembunyikan |
| Enforcement | Blade `@premium` (UI) + Middleware (server) | — |
