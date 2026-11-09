<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Komparasi Pemasok - Namabiz</title>
    <style>
        @page {
            margin: 20pt 25pt;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #111827;
            line-height: 1.35;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-bottom: 2pt solid #111827;
            padding-bottom: 8pt;
            margin-bottom: 12pt;
        }
        .brand-title {
            font-size: 16pt;
            font-weight: bold;
            color: #111827;
            letter-spacing: -0.5pt;
        }
        .brand-tagline {
            font-size: 7.5pt;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            margin-top: 2pt;
        }
        .report-meta {
            text-align: right;
            font-size: 8pt;
            color: #374151;
        }
        .badge-premium {
            display: inline-block;
            background-color: #111827;
            color: #ffffff;
            font-size: 7pt;
            font-weight: bold;
            padding: 2pt 6pt;
            border-radius: 3pt;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin-bottom: 3pt;
        }

        /* Title banner */
        .report-title-banner {
            margin-bottom: 10pt;
        }
        .report-title {
            font-size: 13pt;
            font-weight: bold;
            color: #111827;
            margin: 0 0 2pt 0;
        }
        .report-subtitle {
            font-size: 8pt;
            color: #6b7280;
            margin: 0;
        }

        /* Comparison Table */
        .table-compare {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 14pt;
        }
        .table-compare th, .table-compare td {
            border: 1px solid #e5e7eb;
            padding: 5.5pt 7pt;
            vertical-align: top;
            font-size: 8pt;
            word-wrap: break-word;
        }

        /* Metric label column */
        .col-metric {
            width: 20%;
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .col-vendor {
            width: {{ count($suppliers) > 0 ? (80 / count($suppliers)) : 80 }}%;
        }

        /* Section Separator Row */
        .tr-section td {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: bold;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            padding: 4pt 7pt;
            border-top: 1.5pt solid #d1d5db;
            border-bottom: 1pt solid #d1d5db;
        }

        /* Vendor Header Card */
        .vendor-name {
            font-size: 10pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 2pt;
        }
        .vendor-cat {
            font-size: 7.5pt;
            color: #6b7280;
            margin-bottom: 4pt;
        }
        .badge-verified {
            display: inline-block;
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 1.5pt 5pt;
            border-radius: 3pt;
            text-transform: uppercase;
        }
        .badge-standard {
            display: inline-block;
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            font-size: 6.5pt;
            font-weight: normal;
            padding: 1.5pt 5pt;
            border-radius: 3pt;
        }

        /* Highlight values */
        .val-highlight {
            font-weight: bold;
            color: #111827;
        }
        .val-sub {
            font-size: 7pt;
            color: #6b7280;
            display: block;
            margin-top: 1pt;
        }

        /* Tag pill */
        .pill {
            display: inline-block;
            background-color: #f3f4f6;
            color: #374151;
            font-size: 6.5pt;
            padding: 1pt 4pt;
            border-radius: 2pt;
            margin-right: 2pt;
            margin-bottom: 2pt;
        }

        /* Certification status badges */
        .cert-verified {
            color: #047857;
            font-weight: bold;
        }
        .cert-partial {
            color: #b45309;
        }
        .cert-none {
            color: #9ca3af;
        }

        /* Footer */
        .footer-table {
            width: 100%;
            border-top: 1pt solid #e5e7eb;
            padding-top: 8pt;
            font-size: 7pt;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: middle;">
                <div class="brand-title">NAMABIZ</div>
                <div class="brand-tagline">Direktori & Verifikasi Pasokan Industri B2B</div>
            </td>
            <td style="width: 45%; vertical-align: middle;" class="report-meta">
                <span class="badge-premium">Dokumen Resmi Pengadaan</span>
                <div>No: <strong>{{ $reportNumber ?? 'NBZ-CMP-'.date('Ymd') }}</strong></div>
                <div>Tanggal Cetak: {{ $generatedAt ?? now()->format('d M Y, H:i') }}</div>
                <div>Pemohon: <strong>{{ $user->name ?? 'Tim Pengadaan / Procurement' }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- Title Banner -->
    <div class="report-title-banner">
        <h1 class="report-title">Laporan Komparasi Pemasok & Spesifikasi Pasokan</h1>
        <p class="report-subtitle">
            Perbandingan menyeluruh data profil, kontak langsung, kapasitas pabrik, dan sertifikasi mutu untuk evaluasi tender/pembelian.
        </p>
    </div>

    <!-- Main Comparison Table -->
    <table class="table-compare">
        <!-- VENDOR HEADERS -->
        <thead>
            <tr>
                <th class="col-metric" style="background-color: #111827; color: #ffffff; font-size: 8.5pt;">
                    Parameter Komparasi
                </th>
                @foreach($suppliers as $s)
                    <th class="col-vendor" style="background-color: #f9fafb; text-align: left;">
                        <div class="vendor-name">{{ $s->name }}</div>
                        <div class="vendor-cat">{{ $s->industry_category }}</div>
                        @if($s->is_verified)
                            <span class="badge-verified">&check; Terverifikasi Namabiz</span>
                        @else
                            <span class="badge-standard">Pemasok Standar</span>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <!-- SECTION 1: SALURAN KONTAK LANGSUNG -->
            <tr class="tr-section">
                <td colspan="{{ count($suppliers) + 1 }}">1. Saluran Kontak Langsung (Direct Contact)</td>
            </tr>
            <tr>
                <td class="col-metric">Nomor WhatsApp Resmi</td>
                @foreach($suppliers as $s)
                    <td>
                        @if($s->whatsapp)
                            <span class="val-highlight" style="color: #047857;">{{ $s->whatsapp }}</span>
                            <span class="val-sub">{{ $s->whatsapp_url }}</span>
                        @else
                            <span style="color: #9ca3af;">Tidak tertera</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Email Penjualan / Sales</td>
                @foreach($suppliers as $s)
                    <td>
                        @if($s->email)
                            <span class="val-highlight">{{ $s->email }}</span>
                        @elseif($s->website)
                            <span>sales@{{ ltrim($s->website, 'https://http://') }}</span>
                        @else
                            <span style="color: #9ca3af;">Tidak tertera</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Situs Web Resmi</td>
                @foreach($suppliers as $s)
                    <td>
                        @if($s->website)
                            <span class="val-highlight">{{ $s->website }}</span>
                        @else
                            <span style="color: #9ca3af;">—</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Lokasi Kantor &amp; Pabrik</td>
                @foreach($suppliers as $s)
                    <td>
                        <span class="val-highlight">{{ $s->city ? $s->city . ', ' : '' }}{{ $s->country }}</span>
                    </td>
                @endforeach
            </tr>

            <!-- SECTION 2: KAPABILITAS OPERASIONAL & PRODUKSI -->
            <tr class="tr-section">
                <td colspan="{{ count($suppliers) + 1 }}">2. Kapabilitas Operasional &amp; Produksi</td>
            </tr>
            <tr>
                <td class="col-metric">Minimal Order (MOQ)</td>
                @foreach($suppliers as $s)
                    <td>
                        <span class="val-highlight">{{ $s->moq ?? 'Tersedia Sesuai Diskusi' }}</span>
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Kapasitas Produksi Bulanan</td>
                @foreach($suppliers as $s)
                    <td>
                        <span class="val-highlight">{{ $s->production_capacity ?? 'Kapasitas Kustom' }}</span>
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Lead Time Standar Siap Kirim</td>
                @foreach($suppliers as $s)
                    <td>
                        <span class="val-highlight">{{ $s->avg_lead_time ?? '2-4 Minggu' }}</span>
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Skala Karyawan / Ukuran</td>
                @foreach($suppliers as $s)
                    <td>
                        <span>{{ $s->company_size ?? 'Perusahaan Menengah' }}</span>
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Tahun Pendirian</td>
                @foreach($suppliers as $s)
                    <td>
                        <span>{{ $s->founded_year ? 'Tahun ' . $s->founded_year : '—' }}</span>
                    </td>
                @endforeach
            </tr>

            <!-- SECTION 3: KEPATUHAN, SERTIFIKASI & MUTU -->
            <tr class="tr-section">
                <td colspan="{{ count($suppliers) + 1 }}">3. Standar Sertifikasi &amp; Legalitas</td>
            </tr>
            <tr>
                <td class="col-metric">ISO 9001:2015 (Mutu)</td>
                @foreach($suppliers as $s)
                    @php
                        $iso9001 = $s->certifications->firstWhere('certification.name', 'ISO 9001:2015');
                    @endphp
                    <td>
                        @if($iso9001 && $iso9001->status === 'verified')
                            <span class="cert-verified">&check; Terverifikasi Resmi</span>
                        @elseif($iso9001 && $iso9001->status === 'partial')
                            <span class="cert-partial">&bull; Dalam Proses Audit</span>
                        @else
                            <span class="cert-none">&times; Tidak Terdaftar</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">ISO 14001 (Lingkungan)</td>
                @foreach($suppliers as $s)
                    @php
                        $iso14001 = $s->certifications->firstWhere('certification.name', 'ISO 14001');
                    @endphp
                    <td>
                        @if($iso14001 && $iso14001->status === 'verified')
                            <span class="cert-verified">&check; Terverifikasi Resmi</span>
                        @elseif($iso14001 && $iso14001->status === 'partial')
                            <span class="cert-partial">&bull; Dalam Proses Audit</span>
                        @else
                            <span class="cert-none">&times; Tidak Terdaftar</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Halal MUI / FSSC / HACCP</td>
                @foreach($suppliers as $s)
                    @php
                        $otherCerts = $s->certifications->filter(function($c) {
                            return in_array($c->certification?->name, ['FSSC 22000', 'HACCP', 'Halal MUI']);
                        });
                    @endphp
                    <td>
                        @if($otherCerts->isNotEmpty())
                            @foreach($otherCerts as $oc)
                                <div style="margin-bottom: 2pt;">
                                    <span class="{{ $oc->status === 'verified' ? 'cert-verified' : 'cert-partial' }}">
                                        {{ $oc->status === 'verified' ? '&check;' : '&bull;' }} {{ $oc->certification->name }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <span class="cert-none">Tidak ada sertifikasi higienitas khusus</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Laporan Jejak Karbon / ESG</td>
                @foreach($suppliers as $s)
                    <td>
                        <span>{{ $s->carbon_footprint_report ?? 'Belum dipublikasikan' }}</span>
                    </td>
                @endforeach
            </tr>

            <!-- SECTION 4: SPESIALISASI & DOKUMEN -->
            <tr class="tr-section">
                <td colspan="{{ count($suppliers) + 1 }}">4. Tag Spesialisasi &amp; Berkas Katalog Tersedia</td>
            </tr>
            <tr>
                <td class="col-metric">Keahlian &amp; Bahan Baku</td>
                @foreach($suppliers as $s)
                    <td>
                        @forelse($s->tags as $t)
                            <span class="pill">{{ $t->tag_name }}</span>
                        @empty
                            <span style="color: #9ca3af;">Umum</span>
                        @endforelse
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="col-metric">Berkas Terverifikasi</td>
                @foreach($suppliers as $s)
                    <td>
                        @forelse($s->documents as $doc)
                            <div style="font-size: 7.5pt; margin-bottom: 2pt;">
                                &bull; {{ $doc->title }} ({{ strtoupper($doc->type ?? 'PDF') }})
                            </div>
                        @empty
                            <span style="color: #9ca3af;">Belum ada dokumen publik</span>
                        @endforelse
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <!-- Footer Disclaimer -->
    <table class="footer-table">
        <tr>
            <td style="width: 70%;">
                Dokumen komparasi ini diterbitkan oleh platform <strong>Namabiz</strong> untuk memfasilitasi pengambilan keputusan pengadaan bahan baku B2B. Seluruh data diverifikasi secara berkala berdasarkan arsip dan dokumen legal pemasok.
            </td>
            <td style="width: 30%; text-align: right;">
                Halaman 1 dari 1 &bull; Rahasia &bull; &copy; {{ date('Y') }} Namabiz
            </td>
        </tr>
    </table>
</body>
</html>
