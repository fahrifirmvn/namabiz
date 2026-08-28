# ERD dan Struktur Tabel — SupplyChain Pro

## 1. Identifikasi Entitas

Entitas bisnis inti yang diidentifikasi dari `database/migrations/`:

1. `users` — akun pengguna (Guest yang mendaftar menjadi Free/Premium)
2. `suppliers` — data profil supplier
3. `supplier_tags` — tag/kapabilitas milik supplier (1:N terhadap `suppliers`)
4. `certifications` — master data jenis sertifikasi
5. `supplier_certifications` — tabel pivot status sertifikasi supplier terhadap `certifications` (M:N dengan atribut tambahan)
6. `documents` — dokumen milik supplier (company profile, katalog, dll.)
7. `saved_suppliers` — tabel pivot wishlist user terhadap supplier (M:N)
8. `comparisons` — riwayat perbandingan supplier per user (json `supplier_ids`)
   > ⚠️ Temuan: tabel ini **ada** di migration & model Eloquent (`App\Models\Comparison`), namun **tidak digunakan oleh controller/route manapun** di aplikasi saat ini. Struktur database sudah siap, tetapi fitur belum diimplementasikan secara fungsional.

Tabel infrastruktur bawaan Laravel (`password_reset_tokens`, `sessions`, `cache`, `jobs`) **tidak** disertakan dalam ERD di bawah karena bukan merupakan entitas bisnis domain aplikasi, melainkan tabel pendukung framework.

## 2. Entity Relationship Diagram (Notasi Chen)

Diagram di bawah menggunakan notasi Chen: entitas sebagai kotak persegi, relasi sebagai belah ketupat (diamond) berlabel kata kerja, atribut sebagai elips yang menempel ke entitas/relasinya masing-masing, primary key digarisbawahi, dan kardinalitas (1/N/M) dituliskan pada garis penghubung antara entitas dan diamond. Kolom `supplier_id`/`user_id`/`certification_id` yang berperan sebagai foreign key **tidak** digambar ulang sebagai atribut elips terpisah karena perannya sudah direpresentasikan oleh garis penghubung + kardinalitas menuju diamond relasi.

Tabel `supplier_certifications` dan `saved_suppliers` murni tabel pivot, sehingga digambarkan sebagai **diamond relasi** (bukan entitas kotak). Karena `supplier_certifications` memiliki kolom tambahan bermakna bisnis (`status`, `is_premium_field`), kedua kolom tersebut digambar sebagai atribut yang menempel langsung ke diamond "Memiliki Sertifikasi". Sebaliknya, `saved_suppliers` hanya berisi pasangan FK murni tanpa atribut tambahan, sehingga diamond "Menyimpan (Wishlist)" tidak memiliki atribut menempel.

```plantuml
@startuml SupplyChain_Pro_ERD_Chen
left to right direction
skinparam backgroundColor White
skinparam linetype ortho
skinparam rectangle {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1B2631
}
skinparam ellipse {
  BackgroundColor White
  FontColor #2C3E50
  BorderColor #2C3E50
  FontSize 11
}
skinparam diamond {
  BackgroundColor #E67E22
  FontColor White
  BorderColor #A85400
}

' ===================== ENTITAS =====================
rectangle "Users" as E_USER
rectangle "Suppliers" as E_SUPPLIER
rectangle "SupplierTags" as E_TAG
rectangle "Certifications" as E_CERT
rectangle "Documents" as E_DOC
rectangle "Comparisons\n(belum dipakai fitur manapun)" as E_COMPARE

' ===================== RELASI (diamond) =====================
diamond "Menyimpan\n(Wishlist)" as R_SAVE
diamond "Memiliki\nSertifikasi" as R_SUPCERT
diamond "Memiliki\nDokumen" as R_HASDOC
diamond "Memiliki\nTag" as R_HASTAG
diamond "Membandingkan\n(belum dipakai)" as R_COMPARE

' ===================== ENTITAS - RELASI - ENTITAS (kardinalitas) =====================
E_USER -- R_SAVE : "N"
R_SAVE -- E_SUPPLIER : "M"

E_SUPPLIER -- R_SUPCERT : "N"
R_SUPCERT -- E_CERT : "M"

E_SUPPLIER -- R_HASDOC : "1"
R_HASDOC -- E_DOC : "N"

E_SUPPLIER -- R_HASTAG : "1"
R_HASTAG -- E_TAG : "N"

E_USER -- R_COMPARE : "1"
R_COMPARE -- E_COMPARE : "N"

' ===================== ATRIBUT: Users =====================
usecase "<u>id</u>" as A_U_ID
usecase "name" as A_U_NAME
usecase "email\nUNIQUE" as A_U_EMAIL
usecase "email_verified_at" as A_U_EVAT
usecase "password" as A_U_PASS
usecase "remember_token" as A_U_RTOKEN
usecase "subscription_tier" as A_U_TIER
usecase "premium_expires_at" as A_U_PEXP
usecase "avatar" as A_U_AVATAR
usecase "created_at" as A_U_CAT1
usecase "updated_at" as A_U_UAT1

E_USER -- A_U_ID
E_USER -- A_U_NAME
E_USER -- A_U_EMAIL
E_USER -- A_U_EVAT
E_USER -- A_U_PASS
E_USER -- A_U_RTOKEN
E_USER -- A_U_TIER
E_USER -- A_U_PEXP
E_USER -- A_U_AVATAR
E_USER -- A_U_CAT1
E_USER -- A_U_UAT1

' ===================== ATRIBUT: Suppliers =====================
usecase "<u>id</u>" as A_S_ID
usecase "name" as A_S_NAME
usecase "slug\nUNIQUE" as A_S_SLUG
usecase "logo" as A_S_LOGO
usecase "tagline" as A_S_TAGLINE
usecase "description" as A_S_DESC
usecase "country" as A_S_COUNTRY
usecase "city" as A_S_CITY
usecase "industry_category" as A_S_INDCAT
usecase "is_verified" as A_S_VERIFIED
usecase "about" as A_S_ABOUT
usecase "website" as A_S_WEBSITE
usecase "founded_year" as A_S_FOUNDED
usecase "company_size" as A_S_COMPSIZE
usecase "production_capacity" as A_S_PRODCAP
usecase "moq" as A_S_MOQ
usecase "avg_lead_time" as A_S_LEADTIME
usecase "carbon_footprint_report" as A_S_CARBON
usecase "created_at" as A_S_CAT1
usecase "updated_at" as A_S_UAT1

E_SUPPLIER -- A_S_ID
E_SUPPLIER -- A_S_NAME
E_SUPPLIER -- A_S_SLUG
E_SUPPLIER -- A_S_LOGO
E_SUPPLIER -- A_S_TAGLINE
E_SUPPLIER -- A_S_DESC
E_SUPPLIER -- A_S_COUNTRY
E_SUPPLIER -- A_S_CITY
E_SUPPLIER -- A_S_INDCAT
E_SUPPLIER -- A_S_VERIFIED
E_SUPPLIER -- A_S_ABOUT
E_SUPPLIER -- A_S_WEBSITE
E_SUPPLIER -- A_S_FOUNDED
E_SUPPLIER -- A_S_COMPSIZE
E_SUPPLIER -- A_S_PRODCAP
E_SUPPLIER -- A_S_MOQ
E_SUPPLIER -- A_S_LEADTIME
E_SUPPLIER -- A_S_CARBON
E_SUPPLIER -- A_S_CAT1
E_SUPPLIER -- A_S_UAT1

' ===================== ATRIBUT: SupplierTags =====================
usecase "<u>id</u>" as A_T_ID
usecase "tag_name" as A_T_NAME

E_TAG -- A_T_ID
E_TAG -- A_T_NAME

' ===================== ATRIBUT: Certifications =====================
usecase "<u>id</u>" as A_C_ID
usecase "name" as A_C_NAME
usecase "category" as A_C_CAT

E_CERT -- A_C_ID
E_CERT -- A_C_NAME
E_CERT -- A_C_CAT

' ===================== ATRIBUT: Documents =====================
usecase "<u>id</u>" as A_D_ID
usecase "title" as A_D_TITLE
usecase "type" as A_D_TYPE
usecase "file_path" as A_D_PATH
usecase "external_url" as A_D_URL
usecase "is_premium" as A_D_PREM
usecase "created_at" as A_D_CAT1
usecase "updated_at" as A_D_UAT1

E_DOC -- A_D_ID
E_DOC -- A_D_TITLE
E_DOC -- A_D_TYPE
E_DOC -- A_D_PATH
E_DOC -- A_D_URL
E_DOC -- A_D_PREM
E_DOC -- A_D_CAT1
E_DOC -- A_D_UAT1

' ===================== ATRIBUT: Comparisons (belum dipakai) =====================
usecase "<u>id</u>" as A_CM_ID
usecase "supplier_ids\n(json)" as A_CM_SIDS
usecase "created_at" as A_CM_CAT1
usecase "updated_at" as A_CM_UAT1

E_COMPARE -- A_CM_ID
E_COMPARE -- A_CM_SIDS
E_COMPARE -- A_CM_CAT1
E_COMPARE -- A_CM_UAT1

' ===================== ATRIBUT menempel ke RELASI (supplier_certifications) =====================
usecase "status" as A_R_STATUS
usecase "is_premium_field" as A_R_ISPREM

R_SUPCERT -- A_R_STATUS
R_SUPCERT -- A_R_ISPREM

@enduml
```

> Catatan pembacaan kardinalitas: `1` berarti "satu", `N`/`M` berarti "banyak" (huruf berbeda dipakai pada kedua sisi relasi many-to-many agar tidak ambigu bahwa jumlah "banyak" di tiap sisi tidak harus sama). Contoh: `Users -N- Menyimpan -M- Suppliers` dibaca "satu user dapat menyimpan banyak (M) supplier, dan satu supplier dapat disimpan oleh banyak (N) user" — merepresentasikan relasi M:N melalui tabel pivot `saved_suppliers`.

## 3. Struktur Tabel

### 3.1 Tabel `users`

| Nama Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | bigint unsigned | PRIMARY KEY, auto increment | PK |
| name | string | NOT NULL | |
| email | string | NOT NULL, UNIQUE | |
| email_verified_at | timestamp | NULLABLE | |
| password | string | NOT NULL | disimpan ter-hash (bcrypt) |
| remember_token | string(100) | NULLABLE | |
| subscription_tier | enum('free','premium') | NOT NULL, DEFAULT 'free' | penentu tier akses, tidak mass-assignable |
| premium_expires_at | timestamp | NULLABLE | tidak mass-assignable |
| avatar | string | NULLABLE | |
| created_at, updated_at | timestamp | NULLABLE | |

### 3.2 Tabel `suppliers`

| Nama Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | bigint unsigned | PRIMARY KEY, auto increment | PK |
| name | string | NOT NULL | |
| slug | string | NOT NULL, UNIQUE | |
| logo | string | NULLABLE | |
| tagline | string | NULLABLE | |
| description | text | NULLABLE | |
| country | string | NOT NULL | |
| city | string | NULLABLE | |
| industry_category | string | NOT NULL | |
| is_verified | boolean | NOT NULL, DEFAULT false | |
| about | text | NOT NULL | field gratis |
| website | string | NULLABLE | field gratis |
| founded_year | year | NULLABLE | field premium |
| company_size | string | NULLABLE | field premium |
| production_capacity | string | NULLABLE | field premium |
| moq | string | NULLABLE | field premium |
| avg_lead_time | string | NULLABLE | field premium |
| carbon_footprint_report | string | NULLABLE | field premium |
| created_at, updated_at | timestamp | NULLABLE | |

### 3.3 Tabel `supplier_tags`

| Nama Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | bigint unsigned | PRIMARY KEY, auto increment | PK |
| supplier_id | bigint unsigned | NOT NULL, FOREIGN KEY → `suppliers.id`, ON DELETE CASCADE | FK |
| tag_name | string | NOT NULL | |

Tidak memiliki kolom timestamps (`$timestamps = false` pada model).

### 3.4 Tabel `certifications`

| Nama Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | bigint unsigned | PRIMARY KEY, auto increment | PK |
| name | string | NOT NULL | |
| category | string | NOT NULL | |

Master data, tidak memiliki kolom timestamps.

### 3.5 Tabel `supplier_certifications`

| Nama Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | bigint unsigned | PRIMARY KEY, auto increment | PK |
| supplier_id | bigint unsigned | NOT NULL, FOREIGN KEY → `suppliers.id`, ON DELETE CASCADE | FK |
| certification_id | bigint unsigned | NOT NULL, FOREIGN KEY → `certifications.id`, ON DELETE CASCADE | FK |
| status | enum('verified','not_verified','partial') | NOT NULL, DEFAULT 'verified' | |
| is_premium_field | boolean | NOT NULL, DEFAULT true | menentukan apakah status sertifikasi ini terkunci untuk non-premium |

Tabel pivot yang merealisasikan relasi M:N antara `suppliers` dan `certifications`, dengan atribut tambahan (`status`, `is_premium_field`). Tidak memiliki kolom timestamps.

### 3.6 Tabel `documents`

| Nama Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | bigint unsigned | PRIMARY KEY, auto increment | PK |
| supplier_id | bigint unsigned | NOT NULL, FOREIGN KEY → `suppliers.id`, ON DELETE CASCADE | FK |
| title | string | NOT NULL | |
| type | string | NOT NULL | |
| file_path | string | NULLABLE | path pada disk `public` |
| external_url | string | NULLABLE | alternatif tautan eksternal |
| is_premium | boolean | NOT NULL, DEFAULT true | menentukan gerbang otorisasi unduhan |
| created_at, updated_at | timestamp | NULLABLE | |

### 3.7 Tabel `saved_suppliers`

| Nama Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | bigint unsigned | PRIMARY KEY, auto increment | PK |
| user_id | bigint unsigned | NOT NULL, FOREIGN KEY → `users.id`, ON DELETE CASCADE | FK |
| supplier_id | bigint unsigned | NOT NULL, FOREIGN KEY → `suppliers.id`, ON DELETE CASCADE | FK |
| created_at, updated_at | timestamp | NULLABLE | |

Tabel pivot yang merealisasikan relasi M:N antara `users` dan `suppliers` (wishlist).

### 3.8 Tabel `comparisons`

| Nama Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | bigint unsigned | PRIMARY KEY, auto increment | PK |
| user_id | bigint unsigned | NOT NULL, FOREIGN KEY → `users.id`, ON DELETE CASCADE | FK |
| supplier_ids | json | NOT NULL | daftar ID supplier yang dibandingkan, disimpan sebagai array JSON |
| created_at, updated_at | timestamp | NULLABLE | |

> ⚠️ Tabel ada di skema database, tetapi **belum dipakai oleh fitur manapun** (lihat bagian 4 untuk catatan normalisasi terkait kolom `supplier_ids`).

## 4. Normalisasi

Struktur tabel domain inti (`users`, `suppliers`, `supplier_tags`, `certifications`, `documents`) telah memenuhi **3NF**:

- Setiap tabel memiliki primary key tunggal (`id`), dan seluruh kolom non-key bergantung penuh pada PK tersebut (tidak ada *partial dependency*).
- Tidak ditemukan redundansi data antar tabel — atribut supplier (mis. `industry_category`, `country`) hanya disimpan sekali pada tabel `suppliers`, bukan diulang pada tabel relasi.
- Tidak ditemukan dependensi transitif — misalnya kolom `certifications.category` hanya bergantung pada `certifications.id`, bukan bergantung pada kolom non-key lain.

**Relasi M:N ditangani dengan benar** melalui tabel pivot bertingkat:

- `supplier_certifications` merealisasikan M:N antara `suppliers` dan `certifications`, sekaligus membawa atribut tambahan yang memang melekat pada relasi tersebut (`status`, `is_premium_field`) — ini adalah desain pivot table yang tepat, bukan pelanggaran normalisasi.
- `saved_suppliers` merealisasikan M:N antara `users` dan `suppliers` (wishlist) secara bersih, hanya menyimpan pasangan FK tanpa data ganda.

**Catatan/temuan atas kolom `comparisons.supplier_ids` (tipe `json`):**

Kolom ini menyimpan array ID supplier sebagai satu nilai JSON dalam satu baris. Secara teknis, jika dipandang sebagai representasi relasi multi-nilai (satu comparison memiliki banyak supplier), pendekatan ini **melanggar 1NF** (*atomicity* — satu kolom seharusnya menyimpan satu nilai atom, bukan koleksi nilai). Pendekatan normalisasi yang lebih ketat akan memisahkannya menjadi tabel pivot tersendiri (mis. `comparison_supplier` dengan kolom `comparison_id`, `supplier_id`).

Namun demikian, ini dicatat sebagai **trade-off desain yang pragmatis**, bukan kesalahan murni — pola "snapshot" JSON semacam ini umum digunakan ketika data yang disimpan bersifat *point-in-time* (representasi tetap dari kombinasi supplier saat perbandingan dibuat) dan tidak memerlukan query relasional per-item (mis. "tampilkan semua comparison yang mengandung supplier X"). Karena fitur ini pada kenyataannya **belum dipakai sama sekali** oleh aplikasi, trade-off ini murni bersifat potensial dan perlu dievaluasi ulang jika fitur riwayat perbandingan benar-benar diimplementasikan ke depannya.

## 5. Relasi Antar Tabel (Ringkasan PK/FK)

Ringkasan berikut ditulis dalam notasi relasi PK/FK biasa (bukan notasi Chen pada bagian 2) sebagai referensi teknis cepat bagi pengembang.

| Tabel Asal | Kolom FK | Tabel Tujuan | Jenis Relasi |
|---|---|---|---|
| `supplier_tags` | `supplier_id` | `suppliers.id` | 1:N (satu supplier memiliki banyak tag) |
| `documents` | `supplier_id` | `suppliers.id` | 1:N (satu supplier memiliki banyak dokumen) |
| `supplier_certifications` | `supplier_id` | `suppliers.id` | 1:N (sisi pivot menuju supplier) |
| `supplier_certifications` | `certification_id` | `certifications.id` | 1:N (sisi pivot menuju certification) |
| *(gabungan keduanya)* | — | — | **M:N** `suppliers` ↔ `certifications` melalui `supplier_certifications` |
| `saved_suppliers` | `user_id` | `users.id` | 1:N (sisi pivot menuju user) |
| `saved_suppliers` | `supplier_id` | `suppliers.id` | 1:N (sisi pivot menuju supplier) |
| *(gabungan keduanya)* | — | — | **M:N** `users` ↔ `suppliers` melalui `saved_suppliers` (wishlist) |
| `comparisons` | `user_id` | `users.id` | 1:N (belum dipakai fitur manapun) |
