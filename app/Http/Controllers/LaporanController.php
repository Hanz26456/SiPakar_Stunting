<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\Diagnosis;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    public function bidan(Request $request): Response
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $laporan = Diagnosis::with(['kunjungan.balita', 'verifikator', 'kader'])
            ->whereHas('kunjungan', fn($q) =>
                $q->whereMonth('tanggal_kunjungan', $bulan)
                  ->whereYear('tanggal_kunjungan', $tahun)
            )
            ->get()
            ->map(fn($d) => [
                'id'                => $d->id,
                'nama_balita'       => $d->kunjungan->balita->nama,
                'usia_format'       => $d->kunjungan->balita->usia_format,
                'tanggal'           => $d->kunjungan->tanggal_kunjungan->format('d M Y'),
                'cf_kombinasi'      => $d->cf_kombinasi,
                'cf_persen'         => $d->cf_persen,
                'status_sistem'     => $d->status_stunting,
                'status_final'      => $d->status_final,
                'label_status'      => $d->label_status,
                'warna_status'      => $d->warna_status,
                'sudah_diverifikasi'=> $d->sudah_diverifikasi,
                'verifikator'       => $d->verifikator?->name,
                'tindak_lanjut'     => $d->tindak_lanjut,
                'jadwal_kontrol'    => $d->jadwal_kontrol?->format('d M Y'),
                'kader'             => $d->kader->name,
            ]);

        // Ringkasan statistik bulan ini
        $ringkasan = [
            'total'          => $laporan->count(),
            'normal'         => $laporan->where('status_final', 'normal')->count(),
            'berisiko'       => $laporan->where('status_final', 'berisiko')->count(),
            'stunting'       => $laporan->whereIn('status_final', ['stunting', 'stunting_berat'])->count(),
            'terverifikasi'  => $laporan->where('sudah_diverifikasi', true)->count(),
        ];

        return Inertia::render('Bidan/Laporan', [
            'laporan'   => $laporan,
            'ringkasan' => $ringkasan,
            'filter'    => ['bulan' => $bulan, 'tahun' => $tahun],
        ]);
    }

    /**
     * Ekspor laporan ke PDF.
     * Install: composer require barryvdh/laravel-dompdf
     */
    public function export(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $laporan = Diagnosis::with(['kunjungan.balita', 'verifikator'])
            ->whereHas('kunjungan', fn($q) =>
                $q->whereMonth('tanggal_kunjungan', $bulan)
                  ->whereYear('tanggal_kunjungan', $tahun)
            )
            ->get();

        // Gunakan DomPDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.laporan', [
            'laporan' => $laporan,
            'bulan'   => \Carbon\Carbon::create($tahun, $bulan)->format('F Y'),
            'tanggal' => now()->format('d M Y'),
        ]);

        return $pdf->download("laporan-stunting-{$bulan}-{$tahun}.pdf");
    }
}
