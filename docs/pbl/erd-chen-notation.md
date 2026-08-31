# ERD Notasi Chen — SupplyChain Pro

Diagram *Entity-Relationship* (ERD) dengan **notasi Chen klasik** menggambarkan struktur data secara konseptual: entitas sebagai kotak, relasi sebagai belah ketupat berlabel kata kerja, dan atribut sebagai elips yang menempel langsung ke entitas/relasinya. Notasi ini dipilih karena lebih eksplisit dalam memisahkan "apa itu entitas", "apa itu relasi antar entitas", dan "apa saja atribut yang melekat pada masing-masing", dibandingkan notasi tabel/crow's foot yang lebih ringkas namun kurang menonjolkan semantik relasi.

> Dokumen ini **berdiri sendiri**, terpisah dari [acara-4-erd-struktur-tabel.md](acara-4-erd-struktur-tabel.md) yang berisi struktur tabel teknis lengkap (tipe data, constraint, PK/FK).

## Keputusan Pemecahan Diagram

Project ini memiliki 7 entitas dengan total puluhan atribut. Memaksakan seluruhnya ke dalam satu diagram Graphviz/PlantUML akan menghasilkan tumpang tindih garis dan atribut yang sulit dibaca. Diagram karena itu **dipecah menjadi 3 klaster** berdasarkan kedekatan relasi bisnis:

1. **Klaster 1 — Supplier & Sertifikasi**: `Suppliers` (atribut lengkap ditampilkan di sini) dan `Certifications`, dihubungkan relasi "Mendapat Sertifikasi" (merepresentasikan pivot `supplier_certifications`, termasuk atribut tambahannya `status` dan `is_premium_field`).
2. **Klaster 2 — Supplier & Dokumen & Tag**: `Suppliers` (ditampilkan ringkas — atribut lengkap ada di Klaster 1), `Documents`, dan `SupplierTags`.
3. **Klaster 3 — User, Wishlist & Perbandingan**: `Users` (atribut lengkap), `Suppliers` (ringkas — atribut lengkap ada di Klaster 1), dan `Comparisons` (ditandai sebagai fitur belum diimplementasikan).

Entitas `Suppliers` sengaja ditampilkan ringkas (hanya `id` dan `name`) di Klaster 2 & 3 agar tidak mengulang ke-19 atributnya tiga kali dan membuat diagram lebih ringkas — ditandai dengan *note* yang merujuk ke Klaster 1 sebagai sumber atribut lengkap.

---

## Klaster 1 — Supplier & Sertifikasi

```plantuml
@startuml SupplyChain_Chen_Cluster1
left to right direction
skinparam backgroundColor White
skinparam defaultTextAlignment center

skinparam rectangle {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1A252F
  RoundCorner 0
}
skinparam diamond {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1A252F
}
skinparam ellipse {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1A252F
}

rectangle "Supplier" as E_SUPPLIER
rectangle "Certification" as E_CERT
diamond "Mendapat Sertifikasi" as R_CERT

E_SUPPLIER -- R_CERT : Many
R_CERT -- E_CERT : Many

' Atribut Supplier (lengkap)
ellipse "<u>id</u>" as A_SUP_ID
ellipse "name" as A_SUP_NAME
ellipse "slug (UNIQUE)" as A_SUP_SLUG
ellipse "logo" as A_SUP_LOGO
ellipse "tagline" as A_SUP_TAGLINE
ellipse "description" as A_SUP_DESC
ellipse "country" as A_SUP_COUNTRY
ellipse "city" as A_SUP_CITY
ellipse "industry_category" as A_SUP_INDCAT
ellipse "is_verified" as A_SUP_VERIFIED
ellipse "about" as A_SUP_ABOUT
ellipse "website" as A_SUP_WEBSITE
ellipse "founded_year" as A_SUP_FOUNDED
ellipse "company_size" as A_SUP_COMPSIZE
ellipse "production_capacity" as A_SUP_PRODCAP
ellipse "moq" as A_SUP_MOQ
ellipse "avg_lead_time" as A_SUP_LEADTIME
ellipse "carbon_footprint_report" as A_SUP_CARBON
ellipse "created_at" as A_SUP_CAT
ellipse "updated_at" as A_SUP_UAT

E_SUPPLIER -- A_SUP_ID
E_SUPPLIER -- A_SUP_NAME
E_SUPPLIER -- A_SUP_SLUG
E_SUPPLIER -- A_SUP_LOGO
E_SUPPLIER -- A_SUP_TAGLINE
E_SUPPLIER -- A_SUP_DESC
E_SUPPLIER -- A_SUP_COUNTRY
E_SUPPLIER -- A_SUP_CITY
E_SUPPLIER -- A_SUP_INDCAT
E_SUPPLIER -- A_SUP_VERIFIED
E_SUPPLIER -- A_SUP_ABOUT
E_SUPPLIER -- A_SUP_WEBSITE
E_SUPPLIER -- A_SUP_FOUNDED
E_SUPPLIER -- A_SUP_COMPSIZE
E_SUPPLIER -- A_SUP_PRODCAP
E_SUPPLIER -- A_SUP_MOQ
E_SUPPLIER -- A_SUP_LEADTIME
E_SUPPLIER -- A_SUP_CARBON
E_SUPPLIER -- A_SUP_CAT
E_SUPPLIER -- A_SUP_UAT

' Atribut Certification
ellipse "<u>id</u>" as A_CERT_ID
ellipse "name" as A_CERT_NAME
ellipse "category" as A_CERT_CAT

E_CERT -- A_CERT_ID
E_CERT -- A_CERT_NAME
E_CERT -- A_CERT_CAT

' Atribut menempel ke relationship (kolom milik pivot supplier_certifications)
ellipse "status" as A_CERT_STATUS
ellipse "is_premium_field" as A_CERT_PREMIUM

R_CERT -- A_CERT_STATUS
R_CERT -- A_CERT_PREMIUM

@enduml
```

---

## Klaster 2 — Supplier & Dokumen & Tag

```plantuml
@startuml SupplyChain_Chen_Cluster2
left to right direction
skinparam backgroundColor White
skinparam defaultTextAlignment center

skinparam rectangle {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1A252F
  RoundCorner 0
}
skinparam diamond {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1A252F
}
skinparam ellipse {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1A252F
}

rectangle "Supplier" as E_SUPPLIER
rectangle "Document" as E_DOC
rectangle "SupplierTag" as E_TAG
diamond "Memiliki Dokumen" as R_HASDOC
diamond "Memiliki Tag" as R_HASTAG

E_SUPPLIER -- R_HASDOC : 1
R_HASDOC -- E_DOC : Many

E_SUPPLIER -- R_HASTAG : 1
R_HASTAG -- E_TAG : Many

' Supplier ditampilkan ringkas di klaster ini
ellipse "<u>id</u>" as A_SUP_ID
ellipse "name" as A_SUP_NAME
E_SUPPLIER -- A_SUP_ID
E_SUPPLIER -- A_SUP_NAME
note bottom of E_SUPPLIER : Atribut lengkap Supplier\nlihat Klaster 1

' Atribut Document
ellipse "<u>id</u>" as A_DOC_ID
ellipse "title" as A_DOC_TITLE
ellipse "type" as A_DOC_TYPE
ellipse "file_path" as A_DOC_PATH
ellipse "external_url" as A_DOC_URL
ellipse "is_premium" as A_DOC_PREM
ellipse "created_at" as A_DOC_CAT
ellipse "updated_at" as A_DOC_UAT

E_DOC -- A_DOC_ID
E_DOC -- A_DOC_TITLE
E_DOC -- A_DOC_TYPE
E_DOC -- A_DOC_PATH
E_DOC -- A_DOC_URL
E_DOC -- A_DOC_PREM
E_DOC -- A_DOC_CAT
E_DOC -- A_DOC_UAT

' Atribut SupplierTag
ellipse "<u>id</u>" as A_TAG_ID
ellipse "tag_name" as A_TAG_NAME

E_TAG -- A_TAG_ID
E_TAG -- A_TAG_NAME

@enduml
```

---

## Klaster 3 — User, Wishlist & Perbandingan

```plantuml
@startuml SupplyChain_Chen_Cluster3
left to right direction
skinparam backgroundColor White
skinparam defaultTextAlignment center

skinparam rectangle {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1A252F
  RoundCorner 0
}
skinparam diamond {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1A252F
}
skinparam ellipse {
  BackgroundColor #2C3E50
  FontColor White
  BorderColor #1A252F
}

rectangle "User" as E_USER
rectangle "Supplier" as E_SUPPLIER
rectangle "Comparison" as E_COMPARE
diamond "Menyimpan" as R_SAVE
diamond "Membandingkan" as R_COMPARE

E_USER -- R_SAVE : Many
R_SAVE -- E_SUPPLIER : Many

E_USER -- R_COMPARE : 1
R_COMPARE -- E_COMPARE : Many

' Atribut User (lengkap)
ellipse "<u>id</u>" as A_USER_ID
ellipse "name" as A_USER_NAME
ellipse "email (UNIQUE)" as A_USER_EMAIL
ellipse "email_verified_at" as A_USER_EVAT
ellipse "password" as A_USER_PASS
ellipse "remember_token" as A_USER_RTOKEN
ellipse "subscription_tier" as A_USER_TIER
ellipse "premium_expires_at" as A_USER_PEXP
ellipse "avatar" as A_USER_AVATAR
ellipse "created_at" as A_USER_CAT
ellipse "updated_at" as A_USER_UAT

E_USER -- A_USER_ID
E_USER -- A_USER_NAME
E_USER -- A_USER_EMAIL
E_USER -- A_USER_EVAT
E_USER -- A_USER_PASS
E_USER -- A_USER_RTOKEN
E_USER -- A_USER_TIER
E_USER -- A_USER_PEXP
E_USER -- A_USER_AVATAR
E_USER -- A_USER_CAT
E_USER -- A_USER_UAT

' Supplier ditampilkan ringkas di klaster ini
ellipse "<u>id</u>" as A_SUP_ID
ellipse "name" as A_SUP_NAME
E_SUPPLIER -- A_SUP_ID
E_SUPPLIER -- A_SUP_NAME
note bottom of E_SUPPLIER : Atribut lengkap Supplier\nlihat Klaster 1

' Atribut Comparison (fitur belum diimplementasikan)
ellipse "<u>id</u>" as A_CMP_ID
ellipse "supplier_ids (json)" as A_CMP_SIDS
ellipse "created_at" as A_CMP_CAT
ellipse "updated_at" as A_CMP_UAT

E_COMPARE -- A_CMP_ID
E_COMPARE -- A_CMP_SIDS
E_COMPARE -- A_CMP_CAT
E_COMPARE -- A_CMP_UAT
note bottom of E_COMPARE : Struktur data tersedia,\nbelum dipakai controller manapun

@enduml
```

---

## Tabel Keterangan Entitas & Atribut

| Entity | Deskripsi | Atribut (PK ditandai) |
|---|---|---|
| **Supplier** | Data profil supplier/perusahaan yang terdaftar di platform | **id (PK)**, name, slug, logo, tagline, description, country, city, industry_category, is_verified, about, website, founded_year, company_size, production_capacity, moq, avg_lead_time, carbon_footprint_report, created_at, updated_at |
| **Certification** | Master data jenis sertifikasi industri | **id (PK)**, name, category |
| **Document** | Dokumen milik supplier (company profile, katalog, dll.) | **id (PK)**, title, type, file_path, external_url, is_premium, created_at, updated_at |
| **SupplierTag** | Tag/kapabilitas yang melekat pada supplier | **id (PK)**, tag_name |
| **User** | Akun pengguna platform (Guest yang mendaftar menjadi Free/Premium) | **id (PK)**, name, email, email_verified_at, password, remember_token, subscription_tier, premium_expires_at, avatar, created_at, updated_at |
| **Comparison** | Riwayat perbandingan supplier per user (⚠️ belum dipakai fitur manapun) | **id (PK)**, supplier_ids (json), created_at, updated_at |

Atribut yang menempel pada relasi **"Mendapat Sertifikasi"** (bukan pada entity manapun, karena berasal dari kolom pivot `supplier_certifications`): `status`, `is_premium_field`.

## Catatan

- Entity `Comparisons` dipetakan dalam ERD ini sebagai representasi struktur data yang tersedia di database, namun berdasarkan hasil analisis kode, fitur ini belum diimplementasikan pada layer controller/aplikasi.
- Diagram ini menggunakan notasi Chen klasik untuk kebutuhan visualisasi konseptual. Untuk referensi teknis struktur tabel (tipe data, constraint, PK/FK detail), lihat dokumen `acara-4-erd-struktur-tabel.md`.
