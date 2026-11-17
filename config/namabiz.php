<?php

return [
    'official_hosts' => ['namabiz.com', 'www.namabiz.com', 'namaa.biz.id', 'www.namaa.biz.id'],
    'trusted_proxies' => env('TRUSTED_PROXIES', '127.0.0.1,::1'),

    'contact' => [
        'email' => env('NAMABIZ_CONTACT_EMAIL'),
        'address' => env('NAMABIZ_COMPANY_ADDRESS'),
        'whatsapp' => env('NAMABIZ_CURATOR_WHATSAPP'),
        'whatsapp_portal_source' => env('NAMABIZ_CURATOR_WHATSAPP_SOURCE'),
        'whatsapp_message' => 'Halo tim Namabiz, saya membutuhkan bantuan untuk mencari pemasok.',
        'social' => [
            'Instagram' => env('NAMABIZ_INSTAGRAM_URL'),
            'LinkedIn' => env('NAMABIZ_LINKEDIN_URL'),
        ],
    ],

    /*
     * These integrations are not production-ready. Keep their UI disabled until
     * verified web flows, storage, and server-side purchase verification exist.
     */
    'features' => [
        'google_login' => false,
        'whatsapp_otp' => false,
        'registration_company' => false,
        'registration_role' => false,
        'registration_whatsapp' => false,
        'web_checkout' => false,
        'play_billing_verification' => false,
    ],

    'auth_options' => [
        'google_login' => ['label' => 'Lanjut dengan Google', 'route' => null],
        'whatsapp_otp' => ['label' => 'Masuk dengan OTP WhatsApp', 'route' => null],
    ],

    'links' => [
        'play_cancellation' => 'https://support.google.com/googleplay/answer/7018481?hl=id',
        'play_payments' => 'https://support.google.com/googleplay/answer/2651410?hl=id',
        'play_subscriptions' => 'https://play.google.com/store/account/subscriptions',
    ],

    'billing' => [
        'provider' => 'google_play',
        'products' => ['monthly' => 'business_pro_monthly', 'annual' => 'business_pro_annual'],
        'price' => ['amount' => 150000, 'currency' => 'IDR', 'period' => 'bulan', 'source' => 'owner'],
        'availability' => 'Pembelian Premium belum tersedia di portal ini. Pelajari informasi pembayaran sebelum berlangganan.',
    ],

    'draft' => [
        'label' => '[DRAF - PERLU REVIEW]',
        'notice' => 'Naskah kerja untuk ditinjau pemilik Namabiz. Teks legal belum final dan memerlukan review hukum.',
        'verification' => 'Status Terverifikasi adalah penanda profil yang dapat diubah admin Namabiz. Status sertifikasi dicatat terpisah sebagai terverifikasi, sebagian, atau belum terverifikasi. Penanda ini bukan bukti audit fisik pabrik atau validasi otomatis oleh penerbit sertifikat.',
        'operations' => 'Kapasitas produksi, MOQ, dan lead time berasal dari kolom profil pemasok. Tampilan menggunakan format Indonesia, misalnya 2,5 juta unit/bln, 10.000 unit, dan 4–6 minggu. Angka tersebut contoh format, bukan metrik pemasok tertentu. Data kosong ditandai Belum tersedia. Filter numerik dan waktu respons belum tersedia karena belum ada data terstruktur yang mendukungnya.',
        'payments' => 'Implementasi Android menggunakan Google Play Billing. Metode pembayaran yang dapat digunakan mengikuti pilihan yang ditawarkan Google Play untuk akun, wilayah, dan langganan Anda. Portal tidak menyediakan pembayaran langsung; tidak ada metode bank, dompet digital, atau QRIS Namabiz yang dijanjikan.',
        'cancellation' => 'Untuk langganan berulang yang dibeli melalui Google Play, kelola atau batalkan dari menu Langganan pada akun Google yang digunakan saat membeli. Menghapus aplikasi tidak membatalkan langganan. Menurut Google Play, akses umumnya tetap berlaku hingga akhir periode yang telah dibayar; paket prabayar memiliki ketentuan berbeda. Ketentuan produksi Namabiz dan sinkronisasi status langganan masih perlu disahkan sebelum pembelian diaktifkan.',
    ],

    'pages' => [
        'privacy' => [
            'path' => '/privasi', 'route' => 'pages.privacy', 'title' => 'Privasi', 'group' => 'Legal & bantuan',
            'summary' => 'Ringkasan awal tentang data yang digunakan pada akun dan fitur Namabiz.',
            'legal' => true,
            'sections' => [
                ['title' => 'Data akun dan aktivitas', 'body' => 'Pendaftaran email menggunakan nama, alamat email, dan kata sandi yang disimpan dalam bentuk hash. Aplikasi juga menyimpan sesi atau token autentikasi, pemasok tersimpan, koleksi, perbandingan, serta ulasan yang Anda kirim melalui fitur terkait.'],
                ['title' => 'Penggunaan data pada fitur', 'body' => 'Data akun digunakan untuk masuk dan menentukan akses fitur. Data pemasok tersimpan dan koleksi ditautkan ke akun Anda. Kontak pemasok ditampilkan pada profil sesuai data yang tersedia. Pembayaran Android menggunakan alur Google Play; portal belum menyediakan checkout produksi.'],
                ['title' => 'Bagian yang memerlukan pengesahan', 'body' => 'Identitas pengelola data, masa retensi, prosedur penghapusan akun, hak pengguna, pihak pemroses, dan kanal permintaan privasi harus disahkan pemilik dan penasihat hukum. Draf ini tidak menyatakan bahwa prosedur tersebut sudah tersedia.'],
            ],
        ],
        'terms' => [
            'path' => '/syarat-ketentuan', 'route' => 'pages.terms', 'title' => 'Syarat & Ketentuan', 'group' => 'Legal & bantuan',
            'summary' => 'Draf ruang lingkup penggunaan direktori, data pemasok, dan akses Premium.',
            'legal' => true,
            'sections' => [
                ['title' => 'Ruang lingkup direktori', 'body' => 'Namabiz menyediakan penelusuran profil pemasok, penyimpanan pilihan, perbandingan, dokumen sesuai hak akses, serta tautan kontak pemasok. Ketersediaan informasi mengikuti isi katalog.'],
                ['title' => 'Evaluasi sebelum bekerja sama', 'body' => 'Periksa data, dokumen, dan rincian penawaran langsung dengan pemasok sebelum mengambil keputusan pengadaan. Status profil dan sertifikasi merupakan catatan dalam katalog, bukan jaminan kualitas, pengiriman, atau hasil transaksi.'],
                ['title' => 'Langganan dan pembatalan', 'content' => 'cancellation'],
                ['title' => 'Pengesahan hukum', 'body' => 'Ketentuan kontrak, pembatasan tanggung jawab, penanganan sengketa, pengembalian dana, dan identitas badan usaha belum final. Dokumen ini harus ditinjau secara hukum sebelum dijadikan ketentuan yang mengikat.'],
            ],
            'sources' => ['play_cancellation'],
        ],
        'help' => [
            'path' => '/bantuan', 'route' => 'pages.help', 'title' => 'Bantuan', 'group' => 'Legal & bantuan',
            'summary' => 'Panduan singkat untuk mencari, membandingkan, dan memahami informasi pemasok.',
            'legal' => false,
            'sections' => [
                ['title' => 'Temukan dan hubungi pemasok', 'body' => 'Cari berdasarkan kata kunci, kategori, lokasi, dan sertifikasi. Pilih hingga 4 pemasok untuk dibandingkan. Buka profil untuk menggunakan kontak WhatsApp atau email yang benar-benar tersedia.'],
                ['title' => 'Memahami status verifikasi', 'content' => 'verification'],
                ['title' => 'Satuan data operasional', 'content' => 'operations'],
                ['title' => 'Metode pembayaran', 'content' => 'payments'],
                ['title' => 'Mengelola langganan Google Play', 'content' => 'cancellation'],
            ],
            'sources' => ['play_cancellation', 'play_payments', 'play_subscriptions'],
        ],
        'about' => [
            'path' => '/tentang', 'route' => 'pages.about', 'title' => 'Tentang Namabiz', 'group' => 'Perusahaan',
            'summary' => 'Direktori B2B untuk membantu Anda mengenali calon pemasok.',
            'legal' => false,
            'sections' => [
                ['title' => 'Dari informasi ke pilihan', 'body' => 'Namabiz mengumpulkan profil pemasok dalam katalog yang dapat ditelusuri dan dibandingkan. Anda dapat mempelajari informasi yang tersedia sebelum menghubungi pemasok secara langsung.'],
                ['title' => 'Transparansi informasi', 'content' => 'verification'],
                ['title' => 'Identitas perusahaan', 'body' => 'Informasi badan usaha, alamat, dan kanal resmi akan ditampilkan setelah dikonfirmasi pemilik Namabiz. Tidak ada klaim afiliasi dengan direktori atau perusahaan lain dalam draf ini.'],
            ],
        ],
        'contact' => [
            'path' => '/kontak', 'route' => 'pages.contact', 'title' => 'Kontak', 'group' => 'Perusahaan',
            'summary' => 'Temukan kanal resmi Namabiz yang sudah dikonfirmasi.',
            'legal' => false,
            'sections' => [
                ['title' => 'Kanal resmi', 'body' => 'Hanya kanal yang telah dikonfirmasi pemilik Namabiz yang ditampilkan. Jika belum ada kanal di halaman ini, silakan gunakan panduan di halaman Bantuan untuk memahami fitur yang tersedia.'],
            ],
        ],
    ],
];
