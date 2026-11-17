# GATE — Konten, kontak, billing, dan UI Namabiz

## Hasil implementasi

- Semua kontak resmi, tautan halaman informasi, sumber Google Play, flag kesiapan fitur, dan draf konten dipusatkan di `config/namabiz.php`.
- Lima halaman portal baru: `/privasi`, `/syarat-ketentuan`, `/bantuan`, `/tentang`, `/kontak`. Seluruhnya berlabel **[DRAF - PERLU REVIEW]**; tidak ada teks legal final yang dinyatakan sudah disahkan.
- Tidak ada `[ISI: …]` yang ditampilkan ke pengguna. Kontak kosong/tidak valid tidak menghasilkan baris atau tombol.
- Harga Premium belum tersedia di portal: tampil **Lihat harga di Google Play**, tanpa nominal buatan. Tombol beli nonaktif. `POST /upgrade/checkout` juga ditolak (403), bukan lagi simulasi aktivasi Premium.
- Google login/OTP hanya dapat ditampilkan jika flag aktif dan tersedia route GET web. Endpoint POST aplikasi mobile tidak dianggap sebagai alur login web yang siap.
- Form daftar hanya meminta field yang didukung: email, kata sandi, konfirmasi, dan nama. Perusahaan/peran/WhatsApp tidak ditawarkan.
- Email pemasok tidak lagi ditebak sebagai `sales@domain`, `contact@namabiz.com`, atau `info@namabiz.com`. Tombol kontak hanya memakai data yang tersedia.
- Copy hero: **Gratis untuk memulai. Tanpa Biaya**.
- Efek tambahan: underline hero, glow masuk satu kali, halo fokus pencarian, gradasi CTA, dan progres baca. Animasi dekoratif mengikuti `prefers-reduced-motion`.

## Sumber dan batas audit

1. Backend/portal: repository ini; `.env` hanya diperiksa untuk nilai publik terkait kontak/domain, tanpa menyalin secret.
2. Mobile: `../mobile_namabiz`, dibaca tanpa diubah. Host rilis `namabiz.com` tercantum pada `../mobile_namabiz/app/build.gradle.kts:35`.
3. `https://namabiz.com` dan `https://www.namabiz.com` tidak dapat diakses saat audit (transport error). **Ketiadaan kontak pada hasil audit bukan bukti bahwa kontak tersebut tidak ada di portal produksi.**
4. Tidak menggunakan sumber milik perusahaan/direktori lain. Email seed akun admin, alamat contoh, dan fallback lama tidak dipromosikan menjadi kontak resmi.
5. Dokumen platform yang dibaca:
   - Pembatalan langganan: https://support.google.com/googleplay/answer/7018481?hl=id
   - Metode pembayaran: https://support.google.com/googleplay/answer/2651410?hl=id
   - Pengelolaan langganan: https://play.google.com/store/account/subscriptions

## Inventaris seluruh placeholder

Status **Terisi** berarti data/sumber sudah digunakan; bukan pengesahan hukum. **Draf** berarti teks tersedia dan secara eksplisit belum final. **Kosong** berarti nilai/integrasi belum dapat dipastikan; opsi tersembunyi atau memakai fallback harga yang diminta.

| Placeholder | Status | Sumber (file:baris atau URL) | Aksi pemilik yang masih diperlukan |
| --- | --- | --- | --- |
| Tautan Privasi | Draf | `config/namabiz.php:59`; field akun pada `app/Http/Controllers/Api/AuthController.php:21` | Review `/privasi`, identitas pengelola data, retensi, hak pengguna, pemroses, dan penghapusan akun bersama penasihat hukum. |
| Tautan Syarat & Ketentuan | Draf | `config/namabiz.php:69` | Review `/syarat-ketentuan`, tanggung jawab, sengketa, refund, serta identitas badan usaha. |
| Tautan Bantuan | Draf | `config/namabiz.php:81` | Review `/bantuan` dan kecocokan panduan dengan produk produksi. |
| Tautan Tentang | Draf | `config/namabiz.php:94` | Review `/tentang`; konfirmasi uraian produk dan identitas perusahaan. |
| Tautan Kontak | Draf | `config/namabiz.php:104` | Review `/kontak` dan isi kanal resmi yang dapat dibuktikan. |
| Ketentuan berlangganan/pembatalan | Draf | `config/namabiz.php:55`; `../mobile_namabiz/app/src/main/java/com/namabiz/agrim/core/billing/PlayBillingManager.kt:76`; https://support.google.com/googleplay/answer/7018481?hl=id | Sahkan kebijakan dan implementasikan verifikasi serta sinkronisasi langganan produksi. Penjelasan Google Play tidak berarti lifecycle backend Namabiz sudah benar. |
| Metode pembayaran | Draf | `config/namabiz.php:54`; `../mobile_namabiz/app/src/main/java/com/namabiz/agrim/core/billing/PlayBillingManager.kt:95`; https://support.google.com/googleplay/answer/2651410?hl=id | Uji penawaran akun/perangkat produksi. Tidak menjanjikan bank, e-wallet, QRIS, atau transfer langsung Namabiz. Pilihan aktual mengikuti Google Play. |
| Prosedur/cakupan verifikasi | Draf | `config/namabiz.php:52`; `app/Http/Controllers/Admin/SupplierAdminController.php:166`; `app/Models/SupplierCertification.php:14` | Sahkan makna badge dan SOP kurasi. Kode saat ini hanya menunjukkan perubahan penanda profil dan status sertifikasi, bukan audit fisik atau pengecekan otomatis ke penerbit. |
| Satuan/data operasional | Draf | `config/namabiz.php:53`; `app/Http/Requests/Admin/SupplierRequest.php:40`; `app/Support/SupplierFormat.php:7` | Kurasi nilai asli dan tetapkan satuan baku. Normalisasi field numerik serta waktu respons sebelum mengaktifkan filter terkait. Contoh angka di panduan diberi keterangan sebagai contoh format. |
| Opsi Google / OTP WhatsApp | Kosong | Flag `config/namabiz.php:23`; route web kosong `config/namabiz.php:31`; Google mobile `app/Http/Controllers/Api/AuthController.php:72` | Google mobile memiliki endpoint tetapi belum verifikasi keaslian ID token. Selesaikan verifikasi dan alur sesi web. OTP memerlukan endpoint/provider/rate limit/masa berlaku. Tetap tersembunyi. |
| Nama perusahaan / peran Buyer-Pemasok / WhatsApp saat daftar | Kosong | Flag `config/namabiz.php:25`; field API `app/Http/Controllers/Api/AuthController.php:21` | Tambahkan penyimpanan, validasi, otorisasi peran, kontrak API, dan tes sebelum menawarkan field. Nama lengkap yang didukung tetap tersedia. |
| Email resmi | Kosong | Konfigurasi tanpa default `config/namabiz.php:7`; `.env:56` hanya `hello@example.com` | Isi `NAMABIZ_CONTACT_EMAIL` setelah mailbox resmi dikonfirmasi. Email seed/admin/fallback bukan bukti kontak publik. |
| Alamat perusahaan | Kosong | `config/namabiz.php:8`; tidak ditemukan sumber resmi yang dapat dipastikan | Isi `NAMABIZ_COMPANY_ADDRESS` setelah dikonfirmasi pemilik. |
| Akun media sosial resmi | Kosong | `config/namabiz.php:12`; tidak ditemukan akun resmi yang dapat dipastikan | Isi `NAMABIZ_INSTAGRAM_URL` / `NAMABIZ_LINKEDIN_URL` hanya dengan akun resmi milik Namabiz. |
| Harga Premium | Kosong | Quote perangkat: `../mobile_namabiz/app/src/main/java/com/namabiz/agrim/core/billing/PlayBillingManager.kt:57`; `formattedPrice` pada baris 70; fallback `config/namabiz.php:45` | Hubungkan penawaran Google Play yang sah. Jangan hardcode harga atau menganggap produk sudah dipublikasikan. Portal saat ini tidak menerima quote; pembelian tetap nonaktif. |
| Nomor WhatsApp kurator | Kosong | `config/namabiz.php:9`; validasi sumber portal pada `app/Support/PortalContent.php:33`; portal produksi gagal diakses | Salin hanya nomor yang sudah tertulis pada portal resmi. Isi nomor dan URL bukti pada `NAMABIZ_CURATOR_WHATSAPP` serta `NAMABIZ_CURATOR_WHATSAPP_SOURCE`. Keduanya diperlukan. |
| Profil pemasok pratinjau | Terisi | Query database bersama pada `app/Support/CatalogPresentation.php:25`; komponen `resources/views/components/supplier-preview.blade.php:1` | Kurasi data katalog asli. Preview tetap dinamis; tanpa data tampil pesan kosong, bukan perusahaan rekaan. Status katalog tidak membuktikan data demo merupakan perusahaan nyata. |
| Teks legal final | Draf | Penanda `config/namabiz.php:50`; bagian yang perlu disahkan `config/namabiz.php:66` dan `config/namabiz.php:77` | Diserahkan kepada pemilik dan penasihat hukum. Jangan menjadikan draf ini kebijakan final tanpa review. |

## Backlog backend — penghalang produksi

### P0 — jangan aktifkan transaksi/autentikasi alternatif dulu

- `app/Http/Controllers/Api/SubscriptionController.php:28–32` masih menyimulasikan aktivasi satu tahun; tidak memanggil Google Play Developer API. Endpoint API ini **tidak diubah** dalam pekerjaan konten/UI ini. Menonaktifkan tombol dan checkout web tidak menutup endpoint simulasi mobile.
- Verifikasi token pembelian, package name, product ID, status pembayaran, masa berlaku, duplikasi token dan kepemilikan akun; gunakan expiry sebenarnya, bukan `now()->addYear()` untuk semua paket.
- Sinkronkan pembaruan, pembatalan, refund/revoke, grace period, pemulihan pembelian, dan status langganan dari sumber Google Play yang sah.
- `app/Http/Controllers/Api/AuthController.php:74` hanya memeriksa keberadaan ID token di produksi, bukan tanda tangan/issuer/audience/expiry/subject-nya. Lengkapi verifikasi sebelum mengaktifkan Google login mobile maupun web.
- Harga/offer hanya boleh berasal dari quote Google Play sesuai akun/perangkat/paket. Jangan menyalin nominal dari contoh dokumentasi. Periksa pricing phase/offer yang ditampilkan sebelum menyatakan biaya berulang.
- Checkout web sekarang selalu 403, termasuk jika flag diubah. Integrasi produksi memerlukan handler nyata; **bukan sekadar mengganti flag menjadi true**.

### P1 — fitur dan pengesahan

- OTP WhatsApp: endpoint permintaan/verifikasi, provider, rate limit, expiry, perlindungan penyalahgunaan, dan kebijakan privasi.
- Pendaftaran perusahaan/peran/WhatsApp: schema, validasi, kontrol hak akses, API, serta tes. Peran pemasok tidak boleh otomatis memperoleh hak admin.
- Kapasitas/MOQ/lead time: struktur numerik dan satuan kanonis; waktu respons belum mempunyai field sumber.
- Pemilik: konfirmasi kontak, tinjau semua draf, sahkan legal, serta kurasi katalog asli. Database aktif dan proyek Android tidak ditimpa oleh pekerjaan ini.

## Verifikasi

- PHPUnit seluruh suite: **186 tes lulus, 898 assertions**.
- Build Vite berhasil; Pint dijalankan.
- Pemeriksaan layout browser via iframe: **9 halaman × 7 lebar = 63 kasus**, tanpa overflow horizontal atau teks `[ISI: …]`.
- Lebar: 320, 360, 390, 430, 768, 1024, 1440.
- Audit browser Landing dan Bantuan: Accessibility **100**, Best Practices **100**, SEO **100**.
- Ini bukan benchmark Performance mobile dan bukan pengujian perangkat fisik.
- Screenshot akhir tidak tersedia karena jendela browser desktop tidak terlihat saat capture.
- Lingkungan PHP masih mengeluarkan warning pemuatan `pdo_pgsql`; tes SQLite tetap lulus. Tidak ada perubahan ekstensi PHP.
