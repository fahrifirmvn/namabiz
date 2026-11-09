# SupplyChain Pro — Spesifikasi Kontrak API Mobile (Android Native)

Dokumen ini adalah **Kontrak Resmi Antarmuka API (API Contract)** antara Backend Laravel (`web_brosur`) dan Aplikasi Mobile Android Native (Kotlin MAD).

---

## 1. Informasi Dasar & Konvensi

* **Base URL:** `http://<server-ip-atau-domain>/api/v1`
* **Format Data:** JSON (`Content-Type: application/json`, `Accept: application/json`)
* **Autentikasi:** HTTP Bearer Token via **Laravel Sanctum**.
  ```http
  Authorization: Bearer <token_dari_login_atau_register>
  ```

---

## 2. Format Envelope Response Seragam

Semua response dari server dibungkus dalam format standar (*envelope*):

### Respons Sukses (`200 OK` / `201 Created`)
```json
{
  "success": true,
  "message": "Suppliers retrieved successfully",
  "data": { ... },
  "meta": {
    "pagination": {
      "current_page": 1,
      "last_page": 5,
      "per_page": 12,
      "total": 60,
      "has_more": true
    }
  }
}
```

### Respons Gagal / Validasi (`422 Unprocessable Content`)
```json
{
  "success": false,
  "message": "The email field is required.",
  "errors": {
    "email": [
      "The email field is required."
    ]
  }
}
```

### Respons Terkunci Paywall / Forbidden (`403 Forbidden`)
```json
{
  "success": false,
  "message": "This document requires an active Premium subscription.",
  "errors": {
    "paywall": true
  }
}
```

---

## 3. Modul Autentikasi (`/api/v1/auth`)

### 3.1 Registrasi Akun Baru
* **Endpoint:** `POST /api/v1/auth/register`
* **Akses:** Publik
* **Request Body:**
  ```json
  {
    "name": "Budi Santoso",
    "email": "budi@perusahaan.com",
    "password": "password123",
    "device_name": "Samsung Galaxy S24"
  }
  ```
* **Response `201 Created`:**
  ```json
  {
    "success": true,
    "message": "Registration successful",
    "data": {
      "user": {
        "id": 1,
        "name": "Budi Santoso",
        "email": "budi@perusahaan.com",
        "subscription_tier": "free",
        "is_premium": false,
        "premium_expires_at": null,
        "avatar": null
      },
      "token": "1|abcdef123456..."
    }
  }
  ```

### 3.2 Login Email & Password
* **Endpoint:** `POST /api/v1/auth/login`
* **Akses:** Publik
* **Request Body:**
  ```json
  {
    "email": "budi@perusahaan.com",
    "password": "password123",
    "device_name": "Samsung Galaxy S24"
  }
  ```
* **Response `200 OK`:**
  ```json
  {
    "success": true,
    "message": "Login successful",
    "data": {
      "user": {
        "id": 1,
        "name": "Budi Santoso",
        "email": "budi@perusahaan.com",
        "subscription_tier": "free",
        "is_premium": false
      },
      "token": "2|fedcba654321..."
    }
  }
  ```

### 3.3 Login Cepat Google One-Tap (Mobile)
* **Endpoint:** `POST /api/v1/auth/google`
* **Akses:** Publik
* **Request Body:**
  ```json
  {
    "email": "budi.google@gmail.com",
    "name": "Budi Santoso",
    "avatar": "https://lh3.googleusercontent.com/...",
    "device_name": "Google Pixel 8"
  }
  ```
* **Response `200 OK`:** Mengembalikan profil user dan token baru.

### 3.4 Ambil Profil Akun
* **Endpoint:** `GET /api/v1/auth/user`
* **Akses:** Wajib Token (`auth:sanctum`)
* **Response `200 OK`:**
  ```json
  {
    "success": true,
    "message": "User profile retrieved successfully",
    "data": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@perusahaan.com",
      "subscription_tier": "premium",
      "is_premium": true,
      "premium_expires_at": "2027-10-01T12:00:00Z",
      "avatar": null
    }
  }
  ```

### 3.5 Logout
* **Endpoint:** `POST /api/v1/auth/logout`
* **Akses:** Wajib Token (`auth:sanctum`)
* **Response `200 OK`:** Token saat ini dicabut dari database.

---

## 4. Modul Katalog Supplier & Scanner QR

### 4.1 Daftar Katalog Terpaginasi (Tab 1: Discover)
* **Endpoint:** `GET /api/v1/suppliers`
* **Akses:** Publik (Opsional Token: jika menyertakan token, flag `is_saved` mencerminkan status bookmark user)
* **Query Parameters:**
  * `q` atau `search` (string): Kata kunci nama, kategori, atau negara.
  * `category` (string): Filter kategori (misal: `"Packaging Manufacturer"`).
  * `country` (string): Filter negara (misal: `"Indonesia"`).
  * `certification_id` (integer): Filter ID sertifikasi.
  * `sort_by` (string): `relevance` (default), `newest`, atau `company_size`.
  * `per_page` (integer): Jumlah per halaman (default 12, max 50).
  * `page` (integer): Halaman paginasi.
* **Response `200 OK`:**
  ```json
  {
    "success": true,
    "message": "Suppliers retrieved successfully",
    "data": [
      {
        "id": 10,
        "name": "Atlas Packaging",
        "slug": "atlas-packaging",
        "logo": null,
        "tagline": "Sustainable Industrial Packaging Solutions",
        "country": "Indonesia",
        "city": "Surabaya",
        "industry_category": "Packaging Manufacturer",
        "is_verified": true,
        "is_saved": false,
        "tags": ["Corrugated Boxes", "Biodegradable Film"]
      }
    ],
    "meta": {
      "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 12,
        "total": 35,
        "has_more": true
      }
    }
  }
  ```

### 4.2 Detail Supplier & Aturan Data Masking (Paywall)
* **Endpoint:** `GET /api/v1/suppliers/{idOrSlug}`
* **Akses:** Publik (Opsional Token)
* **Perilaku Keamanan (Server-side Paywall Masking):**
  * **User Free / Guest:** `premium_data.is_locked = true` dan seluruh data sensitif bernilai `null`.
  * **User Premium:** `premium_data.is_locked = false` dan seluruh data berisi nilai sebenarnya.
* **Contoh Response User Free / Guest:**
  ```json
  {
    "success": true,
    "data": {
      "id": 10,
      "name": "Atlas Packaging",
      "slug": "atlas-packaging",
      "about": "Atlas Packaging adalah produsen kemasan terkemuka...",
      "website": "https://atlaspackaging.com",
      "is_saved": false,
      "premium_data": {
        "is_locked": true,
        "founded_year": null,
        "company_size": null,
        "production_capacity": null,
        "moq": null,
        "avg_lead_time": null,
        "carbon_footprint_report": null
      },
      "tags": ["Corrugated Boxes", "Biodegradable Film"],
      "certifications": [
        {
          "id": 1,
          "name": "ISO 9001",
          "category": "Quality Management",
          "status": "verified",
          "is_premium_field": false,
          "is_locked": false
        },
        {
          "id": 2,
          "name": "FSSC 22000",
          "category": "Food Safety",
          "status": "locked",
          "is_premium_field": true,
          "is_locked": true
        }
      ],
      "documents": [
        {
          "id": 101,
          "title": "Brosur Umum 2026.pdf",
          "type": "pdf",
          "is_premium": false,
          "can_download": true,
          "download_url": "https://domain.com/api/v1/documents/101/download"
        },
        {
          "id": 102,
          "title": "Laporan Audit Pabrik & Legalitas.pdf",
          "type": "pdf",
          "is_premium": true,
          "can_download": false,
          "download_url": "https://domain.com/api/v1/documents/102/download"
        }
      ]
    }
  }
  ```

### 4.3 QR Code Resolver (CameraX Scanner)
* **Endpoint:** `GET /api/v1/suppliers/qr-resolve?code={raw_qr_content}`
* **Akses:** Publik
* **Deskripsi:** Menerima raw string hasil scan barcode ML Kit di HP (baik berupa URL lengkap `https://supplychainpro.id/suppliers/atlas-packaging`, deep link `supplychainpro://supplier/atlas-packaging`, maupun slug murni `atlas-packaging`) dan langsung mengembalikan objek supplier terkait.
* **Response `200 OK`:** Mengembalikan detail supplier.
* **Response `404 Not Found`:** Jika QR tidak cocok dengan database.

---

## 5. Modul Komparasi & Dokumen

### 5.1 Komparasi Spek Side-by-Side (2 Kolom Mobile)
* **Endpoint:** `POST /api/v1/compare`
* **Akses:** Publik (Opsional Token)
* **Request Body:**
  ```json
  {
    "supplier_ids": [10, 15]
  }
  ```
* **Response `200 OK`:** Mengembalikan array supplier berdampingan dengan status `premium_data` yang tersanitasi sesuai tier penanya.

### 5.2 Ekspor Hasil Komparasi ke PDF
* **Endpoint:** `POST /api/v1/compare/pdf`
* **Akses:** Wajib Token & **Wajib Premium** (`ensure.premium`)
* **Request Body:**
  ```json
  {
    "supplier_ids": [10, 15]
  }
  ```
* **Response `200 OK`:** Stream binary PDF file (`Content-Type: application/pdf`).

### 5.3 Unduh Berkas Dokumen / Brosur
* **Endpoint:** `GET /api/v1/documents/{id}/download`
* **Akses:** Publik untuk dokumen gratis; Wajib Token Premium untuk dokumen premium.
* **Response `200 OK` / `302 Redirect`:** Mengunduh file atau mengalihkan ke CDN/URL eksternal.
* **Response `403 Forbidden`:** Jika dokumen berbayar diakses user biasa.

---

## 6. Modul Koleksi (Bookmark) & Langganan Play Store

### 6.1 Toggle Simpan Supplier (Bookmark)
* **Endpoint:** `POST /api/v1/suppliers/{id}/toggle-save`
* **Akses:** Wajib Token (`auth:sanctum`)
* **Response `200 OK`:**
  ```json
  {
    "success": true,
    "message": "Supplier saved to collection.",
    "data": {
      "is_saved": true,
      "supplier_id": 10
    }
  }
  ```

### 6.2 Ambil Daftar Koleksi Tersimpan
* **Endpoint:** `GET /api/v1/saved-suppliers`
* **Akses:** Wajib Token (`auth:sanctum`)
* **Response `200 OK`:** Mengembalikan array supplier yang telah disimpan user.

### 6.3 Verifikasi Pembelian Google Play Billing
* **Endpoint:** `POST /api/v1/subscription/verify-play-billing`
* **Akses:** Wajib Token (`auth:sanctum`)
* **Request Body:**
  ```json
  {
    "purchase_token": "inapp-purchase-token-xyz-98765",
    "product_id": "supplychain_premium_yearly",
    "order_id": "GPA.1234-5678-9012-34567"
  }
  ```
* **Response `200 OK`:**
  ```json
  {
    "success": true,
    "message": "Google Play purchase verified. Premium membership activated.",
    "data": {
      "user": {
        "id": 1,
        "subscription_tier": "premium",
        "is_premium": true,
        "premium_expires_at": "2027-10-01T12:00:00Z"
      },
      "subscription": {
        "tier": "premium",
        "product_id": "supplychain_premium_yearly",
        "status": "active",
        "expires_at": "2027-10-01T12:00:00Z"
      }
    }
  }
  ```
