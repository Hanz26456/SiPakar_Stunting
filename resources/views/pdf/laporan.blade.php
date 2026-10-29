<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Deteksi Dini Stunting</title>
    <style>
        @page {
            margin: 20px 25px;
            size: A4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1a202c;
            line-height: 1.4;
        }
        /* Kop Surat */
        .header {
            text-align: center;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h3 {
            margin: 2px 0;
            font-size: 13px;
            font-weight: 600;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            color: #4a5568;
        }
        /* Metadata */
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .meta-table td {
            padding: 2px 0;
            font-size: 11px;
        }
        /* Main Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e0;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f7fafc;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .badge-stunting { background-color: #fed7d7; color: #9b2c2c; }
        .badge-berisiko { background-color: #feebc8; color: #7b341e; }
        .badge-normal   { background-color: #c6f6d5; color: #22543d; }
        /* Summary Box */
        .summary-box {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            font-size: 11px;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Posyandu & Puskesmas -->
    <div class="header">
        <h2>Pemerintah Kabupaten Bondowoso · Dinas Kesehatan</h2>
        <h3>Puskesmas Pujer — Posyandu Melati Pujer</h3>
        <p>Alamat: Desa Pujer Baru, Kecamatan Pujer, Kabupaten Bondowoso, Jawa Timur 68271</p>
    </div>

    <!-- Judul Dokumen -->
    <div style="text-align: center; margin-bottom: 12px;">
        <strong style="font-size: 13px; text-decoration: underline;">
            REKAPITULASI HASIL DETEKSI DINI STUNTING & STATUS GIZI BALITA
        </strong>
    </div>

    <!-- Informasi Periode -->
    <table class="meta-table">
        <tr>
            <td style="width: 18%;"><strong>Periode Pemeriksaan</strong></td>
            <td style="width: 32%;">: {{ $bulan }}</td>
            <td style="width: 18%;"><strong>Tanggal Cetak</strong></td>
            <td style="width: 32%;">: {{ $tanggal }}</td>
        </tr>
        <tr>
            <td><strong>Posyandu</strong></td>
            <td>: Melati Pujer</td>
            <td><strong>Metode Diagnostik</strong></td>
            <td>: Certainty Factor & WHO Z-Score 2006</td>
        </tr>
    </table>

    <!-- Ringkasan Statistik -->
    <div class="summary-box">
        <strong>Ringkasan Periode Ini:</strong>
        Total Pemeriksaan: <strong>{{ $laporan->count() }} Balita</strong> |
        Normal: <strong style="color: #22543d;">{{ $laporan->where('status_final', 'normal')->count() }}</strong> |
        Berisiko: <strong style="color: #b7791f;">{{ $laporan->where('status_final', 'berisiko')->count() }}</strong> |
        Stunting: <strong style="color: #e53e3e;">{{ $laporan->whereIn('status_final', ['stunting', 'stunting_berat'])->count() }}</strong> |
        Terverifikasi Bidan: <strong>{{ $laporan->where('sudah_diverifikasi', true)->count() }}</strong>
    </div>

    <!-- Tabel Data Rekapitulasi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 22%;">Nama Balita</th>
                <th style="width: 12%;" class="text-center">Usia</th>
                <th style="width: 12%;" class="text-center">BB / TB</th>
                <th style="width: 12%;" class="text-center">Z-Score TB/U</th>
                <th style="width: 12%;" class="text-center">CF Pakar</th>
                <th style="width: 13%;" class="text-center">Status Final</th>
                <th style="width: 12%;" class="text-center">Validasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->kunjungan?->balita?->nama ?? '-' }}</strong><br>
                        <span style="font-size: 9px; color: #718096;">Ibu: {{ $item->kunjungan?->balita?->nama_ibu ?? '-' }}</span>
                    </td>
                    <td class="text-center">{{ $item->kunjungan?->balita?->usia_format ?? '-' }}</td>
                    <td class="text-center">
                        {{ $item->kunjungan?->berat_badan ?? '-' }} kg<br>
                        {{ $item->kunjungan?->tinggi_badan ?? '-' }} cm
                    </td>
                    <td class="text-center">
                        <strong>{{ number_format($item->kunjungan?->zscore_tbu ?? 0, 2) }} SD</strong>
                    </td>
                    <td class="text-center font-bold">
                        {{ $item->cf_persen }}
                    </td>
                    <td class="text-center">
                        @php
                            $st = $item->status_final;
                            $cls = in_array($st, ['stunting', 'stunting_berat']) ? 'badge-stunting' : ($st === 'berisiko' ? 'badge-berisiko' : 'badge-normal');
                        @endphp
                        <span class="badge {{ $cls }}">
                            {{ $item->label_status }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if ($item->sudah_diverifikasi)
                            <span style="color: #2b6cb0; font-size: 10px;">✓ Diverifikasi</span><br>
                            <span style="font-size: 8px; color: #718096;">{{ $item->verifikator?->name ?? 'Bidan' }}</span>
                        @else
                            <span style="color: #dd6b20; font-size: 10px;">⏳ Menunggu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #a0aec0;">
                        Tidak ada catatan diagnosis untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Pengesahan -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Ketua Posyandu Melati Pujer</strong>
                <div class="signature-space"></div>
                <strong>( Ratna Dewi )</strong>
            </td>
            <td>
                Bondowoso, {{ $tanggal }}<br>
                <strong>Tenaga Kesehatan / Bidan Pembina</strong>
                <div class="signature-space"></div>
                <strong>( Bidan Marini Poeji, A.Md.Keb )</strong><br>
                <span style="font-size: 9px; color: #4a5568;">NIP. 19850412 201001 2 025</span>
            </td>
        </tr>
    </table>

</body>
</html>

