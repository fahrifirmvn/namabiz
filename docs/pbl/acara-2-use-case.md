# Use Case dan Skenario — SupplyChain Pro

## 1. Daftar Use Case Utama

### Aktor: Guest (belum login)

- Registrasi akun baru
- Login ke sistem
- Lupa password / reset password
- Mencari & memfilter supplier (Discover)
- Melihat detail profil supplier (field gratis saja)
- Membandingkan supplier side-by-side (maksimal 3)
  > Catatan temuan: route `/analytics/compare` **tidak** dilindungi middleware `auth`, sehingga fitur perbandingan dasar dapat digunakan tanpa login.
- Mengunduh dokumen supplier yang berstatus gratis

### Aktor: Registered User — Free Tier (tambahan dari Guest)

- Logout
- Verifikasi email, konfirmasi ulang password
- Menyimpan/menghapus supplier dari wishlist
- Melihat halaman upgrade ke Premium
- Melakukan checkout (simulasi) upgrade ke Premium

### Aktor: Registered User — Premium Tier (tambahan dari Free Tier)

- Melihat seluruh field data premium pada profil supplier (`founded_year`, `company_size`, `production_capacity`, `moq`, `avg_lead_time`, `carbon_footprint_report`)
- Melihat data operasional/premium pada tabel perbandingan
- Mengekspor hasil perbandingan supplier ke PDF
- Mengunduh dokumen supplier berstatus premium

## 2. Use Case Diagram

Diagram tersedia dalam format PlantUML pada file [acara-2-use-case.puml](acara-2-use-case.puml) (dapat di-*render* di [plantuml.com/plantuml](https://www.plantuml.com/plantuml) atau plugin PlantUML pada editor kode). Diagram merepresentasikan hierarki aktor sebagai generalisasi (Guest → Free User → Premium User, bukan role terpisah yang berdiri sendiri), sesuai fakta bahwa perbedaan akses murni ditentukan oleh kolom `subscription_tier` pada tabel `users` yang sama.

## 3. Skenario Use Case

### Use Case: Cari & Filter Supplier (Discover Search)

**Aktor:** Guest, Free User, Premium User

**Pre-condition:**
- User mengakses halaman utama (`/`)

**Normal Flow:**
1. Sistem menampilkan daftar supplier terpaginasi (12 per halaman), diurutkan berdasarkan status verifikasi (mode urutan default: relevansi)
2. User mengetikkan kata kunci pencarian pada kolom pencarian
3. Sistem melakukan pencarian *realtime* (Livewire) terhadap kolom `name`, `industry_category`, dan `country` tanpa memuat ulang halaman
4. User memilih filter tambahan: lokasi (`country`), kategori industri (`industry_category`), dan/atau sertifikasi
5. Sistem mereset halaman ke halaman pertama dan menampilkan hasil sesuai kombinasi filter yang dipilih
6. User dapat mengubah urutan hasil (relevansi/terbaru/ukuran perusahaan)

**Alternative Flow:**
- Tidak ada supplier yang cocok dengan kata kunci/filter → sistem menampilkan daftar kosong (*empty state*)
- User menekan "Clear Filters" → filter lokasi/kategori/sertifikasi direset; kata kunci pencarian **tidak** ikut direset karena tersimpan terpisah pada query string URL (`#[Url]`)

**Post-condition:**
- Daftar supplier yang ditampilkan sesuai dengan kombinasi filter aktif

---

### Use Case: Lihat Detail Supplier (dengan Locked Field)

**Aktor:** Guest, Free User, Premium User

**Pre-condition:**
- Supplier dengan `slug` yang valid tersedia di database

**Normal Flow:**
1. User membuka halaman detail supplier (`/suppliers/{slug}`)
2. Sistem memuat data supplier beserta relasi `tags`, `documents`, dan `certifications.certification`
3. Sistem menampilkan field gratis (`about`, `website`, tags) kepada seluruh pengunjung
4. Sistem mengevaluasi status premium user pengakses melalui `PremiumAccess::isPremium()`
5. Jika user berstatus Premium, seluruh field premium (`founded_year`, `company_size`, `production_capacity`, `moq`, `avg_lead_time`, `carbon_footprint_report`) ditampilkan dengan nilai sebenarnya
6. Jika user Guest/Free, field premium ditampilkan dalam kondisi terkunci melalui komponen `<x-locked-field>` **tanpa** menyertakan nilai asli pada markup HTML

**Alternative Flow:**
- User Guest/Free mengklik field terkunci → memicu event Livewire `open-premium-modal` yang menampilkan modal ajakan upgrade
- Supplier dengan `slug` tidak ditemukan → sistem mengembalikan HTTP 404

**Post-condition:**
- Halaman detail supplier tampil dengan tingkat keterbukaan data sesuai status langganan user

---

### Use Case: Simpan Supplier (Wishlist)

**Aktor:** Free User, Premium User

**Pre-condition:**
- User sudah login (route `POST /suppliers/{supplier}/save` dilindungi middleware `auth`)

**Normal Flow:**
1. User menekan tombol simpan/wishlist pada kartu atau halaman detail supplier
2. Sistem mengecek apakah kombinasi `user_id` + `supplier_id` sudah ada pada tabel `saved_suppliers` (dibatasi hanya milik user yang sedang login, bukan berdasarkan input eksternal)
3. Jika belum ada, sistem membuat baris baru (menandai tersimpan)
4. Jika sudah ada, sistem menghapus baris tersebut (toggle, menandai batal disimpan)
5. Sistem mengarahkan kembali (`back()`) ke halaman asal

**Alternative Flow:**
- Guest mencoba mengakses endpoint ini secara langsung → dialihkan oleh middleware `auth` ke halaman login

**Post-condition:**
- Status wishlist supplier untuk user tersebut berubah (ter-*toggle*) sesuai aksi

---

### Use Case: Bandingkan Supplier (Maksimal 3)

**Aktor:** Guest, Free User, Premium User

**Pre-condition:**
- User membuka halaman `/analytics/compare` (tidak memerlukan login)

**Normal Flow:**
1. Sistem menampilkan tabel perbandingan berisi 2 supplier acak sebagai default (`mount()`)
2. User mencari nama supplier yang ingin ditambahkan
3. User memilih supplier dari hasil pencarian → sistem menambahkan ke `$supplierIds` jika belum ada dan jumlah belum mencapai 3
4. Sistem menampilkan kolom perbandingan side-by-side: data dasar untuk seluruh pengunjung, data operasional/premium hanya untuk user Premium (lihat Use Case "Lihat Data Operasional Premium pada Tabel Perbandingan")
5. User dapat menghapus supplier dari tabel perbandingan

**Alternative Flow:**
- User mencoba menambahkan supplier ke-4 → sistem menolak tanpa perubahan (`addSupplier()` mengembalikan langsung tanpa aksi jika jumlah sudah 3)
- Percobaan manipulasi state langsung (crafted request ke endpoint `/livewire/update` memaksa `supplierIds` berisi lebih dari 3 item) → sistem melakukan *re-clamp* otomatis melalui hook `updated()`/`hydrate()`, membatasi kembali ke maksimal 3 item unik

**Post-condition:**
- Tabel perbandingan berisi maksimal 3 supplier yang valid dan unik

---

### Use Case: Export Perbandingan ke PDF

**Aktor:** Premium User

**Pre-condition:**
- User berada di halaman perbandingan dengan minimal 1 supplier dipilih
- User berstatus Premium aktif (`subscription_tier = 'premium'` dan `premium_expires_at` masih berlaku atau `null`)

**Normal Flow:**
1. User menekan tombol "Download Comparison (PDF)"
2. Request diarahkan ke `GET /analytics/compare/download-pdf`, yang dilindungi middleware `ensure.premium`
3. Middleware memvalidasi status premium user melalui `PremiumAccess::isPremium()`
4. Sistem mengambil kembali maksimal 3 `supplier_id` (dari query string, dideduplikasi & dibatasi jumlahnya) beserta relasi sertifikasi dan dokumen
5. Sistem merender view PDF menggunakan DomPDF dan mengunduhkannya sebagai berkas `supplier-comparison.pdf`

**Alternative Flow:**
- User Guest/Free mencoba mengakses endpoint ini langsung via URL → middleware `ensure.premium` mengalihkan ke halaman upgrade (`route('upgrade')`) dengan flag sesi `paywall_triggered`

**Post-condition:**
- Berkas PDF hasil perbandingan berhasil diunduh (khusus user Premium)

---

### Use Case: Download Dokumen Supplier

**Aktor:** Guest & Free User (dokumen gratis) / Premium User (seluruh dokumen)

**Pre-condition:**
- Dokumen dengan ID valid tersedia pada tabel `documents`, memiliki `file_path` dan/atau `external_url`

**Normal Flow:**
1. User menekan tombol unduh pada daftar dokumen di halaman detail supplier
2. Request diarahkan ke `GET /documents/{document}/download`
3. Sistem mengecek kolom `is_premium` dokumen terhadap status premium user pengakses
4. Jika dokumen gratis (`is_premium = false`), lanjut ke langkah 5 untuk siapa saja termasuk Guest
5. Jika dokumen memiliki `external_url`, sistem mengalihkan (redirect) ke URL eksternal tersebut; jika memiliki `file_path` pada disk `public`, sistem mengalirkan berkas tersebut sebagai unduhan

**Alternative Flow:**
- Dokumen berstatus premium (`is_premium = true`) diakses oleh Guest/Free User → sistem mengembalikan HTTP 403 Forbidden, dicegat di layer controller (bukan hanya disembunyikan pada tampilan)
- Dokumen tidak memiliki `file_path` maupun `external_url` yang valid → sistem mengembalikan HTTP 404

**Post-condition:**
- Dokumen berhasil diunduh/diarahkan hanya jika otorisasi terpenuhi

---

### Use Case: Upgrade ke Premium

**Aktor:** Free User

**Pre-condition:**
- User sudah login (route `/upgrade` dan `/upgrade/checkout` dilindungi middleware `auth`)

**Normal Flow:**
1. User membuka halaman `/upgrade` (halaman informasi paket Premium)
2. User menekan tombol upgrade/checkout
3. Sistem memproses `POST /upgrade/checkout`
4. Sistem mengubah `subscription_tier` user menjadi `'premium'` dan `premium_expires_at` menjadi satu tahun ke depan (`now()->addYear()`)
5. Sistem mengarahkan user kembali ke halaman Discover dengan pesan status "You are now a Premium member!"

**Alternative Flow:**
> ⚠️ Catatan: tidak ada validasi/integrasi metode pembayaran sungguhan pada flow ini — proses ini adalah simulasi/dummy checkout, bukan representasi payment gateway yang siap produksi.

**Post-condition:**
- Status langganan user berubah menjadi Premium; seluruh field & fitur premium langsung terbuka pada request berikutnya
