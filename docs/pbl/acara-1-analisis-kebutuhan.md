# Analisis Kebutuhan Sistem — SupplyChain Pro

## 1. Nama/Judul Sistem

**SupplyChain Pro** — Platform B2B *Supplier Discovery & Comparison* dengan model bisnis **Freemium**.

Sumber: [docs/prompt-laravel-supplychain-pro.md](../prompt-laravel-supplychain-pro.md) mendefinisikan aplikasi ini sebagai platform pencarian & perbandingan supplier industri (manufaktur, packaging, raw material), di mana pengguna gratis dapat melakukan browsing & melihat data dasar, sementara data operasional/teknis mendalam terkunci di balik langganan Premium.

## 2. Deskripsi Masalah

Perusahaan yang mencari mitra supplier (manufaktur, packaging, bahan baku) kesulitan menemukan dan membandingkan supplier secara efisien: data supplier tersebar, sulit dibandingkan side-by-side, dan informasi teknis-operasional penting (kapasitas produksi, MOQ, lead time, laporan jejak karbon) seringkali tidak transparan tanpa negosiasi langsung.

SupplyChain Pro menjawab masalah ini dengan:

- Direktori pencarian & filter supplier yang terpusat (lokasi, kategori industri, sertifikasi).
- Tabel perbandingan supplier side-by-side (maksimal 3 supplier sekaligus).
- Model monetisasi **Freemium**: data dasar (nama, deskripsi, website, tags, status verifikasi) terbuka untuk semua pengguna sebagai *lead generation*, sedangkan data operasional bernilai tinggi (kapasitas produksi, MOQ, lead time, dokumen legal/katalog) dikunci di balik langganan Premium sebagai sumber pendapatan.

Berdasarkan kode yang ada, sistem ini **belum** mengimplementasikan pembayaran nyata — flow upgrade ke Premium bersifat simulasi (lihat Kebutuhan Non-Fungsional, bagian Reliabilitas & Keterbatasan).

## 3. Aktor

- **Guest** (pengunjung belum login): dapat mengakses pencarian & filter supplier, melihat detail profil supplier (hanya field gratis), menggunakan tabel perbandingan supplier, dan mengunduh dokumen supplier yang berstatus gratis. Tidak dapat menyimpan wishlist maupun mengakses data/dokumen premium.
- **Registered User (Free Tier)**: seluruh hak akses Guest, ditambah kemampuan menyimpan/menghapus supplier dari wishlist, serta mengakses halaman upgrade dan melakukan checkout ke Premium. Field dan dokumen premium tetap terkunci baginya selama `subscription_tier` bernilai `free` (atau `premium_expires_at` sudah lewat).
- **Registered User (Premium Tier)**: seluruh hak akses Free Tier, ditambah akses penuh ke seluruh field data premium pada profil & tabel perbandingan supplier, unduhan dokumen premium, dan ekspor hasil perbandingan ke PDF.

> Ditegaskan secara eksplisit: **tidak ditemukan role admin/superuser** di manapun dalam basis kode (tidak ada tabel/kolom role, middleware permission, atau panel administrasi). Perbedaan hak akses murni ditentukan oleh kolom `users.subscription_tier` (`free`/`premium`) beserta `users.premium_expires_at`, dievaluasi melalui satu kelas tunggal `App\Support\PremiumAccess`.

## 4. Kebutuhan Fungsional

### Modul Autentikasi

- **FR-01**: Sistem harus dapat memproses registrasi akun baru dengan validasi nama, format & keunikan email, serta konfirmasi password (Livewire Volt, `register.blade.php`).
- **FR-02**: Sistem harus dapat memproses login menggunakan email & password berbasis session.
- **FR-03**: Sistem harus dapat memproses logout (invalidasi session & regenerasi token CSRF).
- **FR-04**: Sistem harus dapat memproses permintaan lupa password (mengirim link reset).
- **FR-05**: Sistem harus dapat memproses reset password menggunakan token yang valid.
- **FR-06**: Sistem harus dapat memverifikasi email pengguna melalui tautan bertanda tangan (*signed URL*) dengan pembatasan laju (`throttle:6,1`).
- **FR-07**: Sistem harus dapat meminta konfirmasi ulang password untuk aksi sensitif (`confirm-password`).

### Modul Discover & Pencarian Supplier

- **FR-08**: Sistem dapat menampilkan daftar supplier dengan paginasi (12 data per halaman).
- **FR-09**: Sistem dapat melakukan pencarian supplier secara *realtime* berdasarkan nama, kategori industri, atau negara tanpa memuat ulang halaman (Livewire).
- **FR-10**: Sistem dapat memfilter supplier berdasarkan lokasi (negara), kategori industri, dan sertifikasi.
- **FR-11**: Sistem dapat mengurutkan hasil pencarian berdasarkan relevansi (status verifikasi), terbaru, atau ukuran perusahaan.
- **FR-12**: Sistem menyimpan kata kunci pencarian pada query string URL (`#[Url]`) agar tautan pencarian dapat dibagikan.

### Modul Detail Profil Supplier

- **FR-13**: Sistem dapat menampilkan detail profil supplier beserta tags, dokumen, dan sertifikasi terkait.
- **FR-14**: Sistem harus membedakan tampilan field gratis (`about`, `website`, tags) dan field premium (`founded_year`, `company_size`, `production_capacity`, `moq`, `avg_lead_time`, `carbon_footprint_report`) berdasarkan status langganan pengguna yang mengakses, tanpa membocorkan nilai asli field premium pada markup HTML saat terkunci.
- **FR-15**: Sistem dapat menampilkan status "tersimpan/wishlist" pada profil supplier bagi pengguna yang sudah login.

### Modul Wishlist

- **FR-16**: Sistem dapat menyimpan atau menghapus (toggle) supplier dari daftar wishlist pengguna, khusus untuk pengguna yang sudah login.

### Modul Perbandingan Supplier

- **FR-17**: Sistem dapat menambahkan supplier ke tabel perbandingan hingga maksimal 3 supplier sekaligus.
- **FR-18**: Sistem dapat mencari supplier untuk ditambahkan ke dalam tabel perbandingan.
- **FR-19**: Sistem dapat menghapus supplier dari tabel perbandingan.
- **FR-20**: Sistem harus mencegah manipulasi jumlah/isi supplier dalam perbandingan melalui request langsung ke endpoint Livewire (guard `updated()`/`hydrate()` pada properti publik `$supplierIds`).
- **FR-21**: Sistem dapat menampilkan data operasional/premium pada tabel perbandingan hanya untuk pengguna Premium.

### Modul Export & Download

- **FR-22**: Sistem dapat mengekspor hasil perbandingan supplier ke berkas PDF, dibatasi hanya untuk pengguna Premium melalui middleware `ensure.premium`.
- **FR-23**: Sistem dapat menyediakan unduhan dokumen supplier yang berstatus gratis (`is_premium = false`) untuk siapa saja, termasuk Guest.
- **FR-24**: Sistem harus mencegah unduhan dokumen berstatus premium oleh pengguna yang bukan Premium (mengembalikan HTTP 403), dicegat di layer controller (`DocumentController`), bukan hanya disembunyikan pada tampilan.

### Modul Manajemen Langganan Premium

- **FR-25**: Sistem dapat menampilkan halaman upgrade ke Premium bagi pengguna yang sudah login.
- **FR-26**: Sistem dapat memproses "checkout" yang mengubah status pengguna menjadi Premium dengan masa berlaku 1 tahun sejak transaksi (`premium_expires_at = now()->addYear()`).
  > ⚠️ Catatan: proses checkout ini bersifat **simulasi/dummy** — langsung mengubah status tanpa validasi pembayaran maupun integrasi payment gateway sungguhan. Lihat detail keterbatasan pada bagian Kebutuhan Non-Fungsional.

### Fitur yang Belum Diimplementasikan (Temuan)

- Tabel `comparisons` dan model `App\Models\Comparison` **sudah dibuat** (migration & Eloquent model lengkap dengan relasi ke `users`), namun **tidak ada satu pun route/controller yang menggunakannya**. Fitur "riwayat perbandingan tersimpan ke database per pengguna" belum diimplementasikan secara fungsional — perbandingan yang berjalan saat ini murni bersifat state sementara pada komponen Livewire (`ComparisonTable`), tidak dipersist ke database. Ini dicatat sebagai temuan/skema yang belum terpakai, bukan Kebutuhan Fungsional yang sudah selesai.

## 5. Kebutuhan Non-Fungsional

### Keamanan

- Password pengguna di-hash menggunakan bcrypt (`'password' => 'hashed'` cast pada model `User`, `BCRYPT_ROUNDS=12`).
- Proteksi CSRF bawaan Laravel diterapkan pada seluruh form/POST request.
- Autentikasi berbasis session (`SESSION_DRIVER=database`), bukan token/JWT.
- Otorisasi status premium diterapkan berlapis namun konsisten dari **satu sumber kebenaran tunggal** (`App\Support\PremiumAccess::isPremium()`), dikonsumsi oleh: Gate `view-premium-data`, middleware `EnsurePremiumUser` (alias `ensure.premium`), Blade directive `@premium`, dan method `User::isPremium()` — tidak ditemukan logika pengecekan premium yang menyimpang di tempat lain.
- Endpoint unduhan dokumen (`DocumentController::download`) melakukan pengecekan otorisasi di layer controller (403 Forbidden untuk dokumen premium yang diakses non-premium), bukan hanya menyembunyikan tautan di tampilan — mencegah akses langsung via curl/devtools.
- Mass assignment dibatasi eksplisit melalui atribut `#[Fillable(['name', 'email', 'password', 'avatar'])]` pada model `User` — kolom sensitif `subscription_tier` dan `premium_expires_at` sengaja **tidak** mass-assignable.
- Properti publik komponen Livewire `ComparisonTable::$supplierIds` memiliki guard tambahan (`updated()`/`hydrate()`) untuk mencegah manipulasi state melalui crafted request langsung ke endpoint `/livewire/update`.

> ⚠️ Perlu konfirmasi manual: validasi input pada form autentikasi (register/login/reset password) terbukti **ada** dan dijalankan melalui pemanggilan `$this->validate()` inline pada komponen Livewire Volt, namun **bukan** dalam bentuk *Form Request* class terpisah — folder `app/Http/Requests` tidak ditemukan/tidak ada di codebase ini.
>
> ⚠️ Perlu konfirmasi manual: `SESSION_SECURE_COOKIE` belum diset eksplisit di `.env` — perlu dipastikan diaktifkan saat deployment produksi dengan HTTPS.
>
> ⚠️ Perlu konfirmasi manual: `APP_DEBUG=true` pada `.env` saat ini (kondisi development) — wajib diset `false` saat produksi untuk mencegah kebocoran stack trace/informasi sensitif.

### Performa

- Paginasi 12 data per halaman pada halaman Discover (mencegah pengambilan seluruh data sekaligus).
- Pencarian & filter bersifat *realtime* tanpa reload halaman penuh (Livewire, partial re-render).

> ⚠️ Perlu konfirmasi manual: target response time, jumlah maksimal pengguna konkuren, atau kebutuhan caching tidak tercermin secara eksplisit di kode (queue & cache masih menggunakan driver `database`/`file` bawaan sesuai `.env`, sesuai untuk skala pengembangan/demo).

### Usability & Kompatibilitas

- Tampilan responsif menggunakan Tailwind CSS 3 (utility-first).
- Ikon antarmuka konsisten menggunakan Blade Heroicons (`blade-ui-kit/blade-heroicons`).

### Reliabilitas & Keterbatasan Implementasi

- Flow upgrade ke Premium bersifat **dummy/simulasi**: `SubscriptionController::checkout()` langsung mengubah `subscription_tier` menjadi `'premium'` tanpa validasi pembayaran, tanpa integrasi payment gateway (mis. Midtrans/Stripe), dan tanpa mekanisme idempoten. Ini adalah simplifikasi yang disengaja untuk keperluan demo/PBL, **bukan** representasi flow pembayaran yang siap produksi.
- Fitur riwayat perbandingan (tabel `comparisons`) belum diimplementasikan secara fungsional (lihat catatan FR di atas) — berpotensi menjadi *technical debt* berupa skema database yang belum terpakai.

### Skalabilitas

> ⚠️ Perlu konfirmasi manual: kebutuhan skalabilitas (target jumlah supplier terdaftar, jumlah pengguna konkuren, dsb.) tidak tercermin dari kode karena merupakan kebutuhan bisnis yang belum diimplementasikan secara eksplisit di level infrastruktur (queue/cache masih menggunakan driver default yang sesuai untuk skala kecil/pengembangan).
