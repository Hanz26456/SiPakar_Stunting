<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\Diagnosis;
use App\Services\KunjunganService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifikasiController extends Controller
{
    public function __construct(
        private readonly KunjunganService $kunjunganService
    ) {}

    /**
     * Monitoring semua balita — tampilan bidan.
     */
    public function index(): Response
    {
        $belumVerifikasi = Diagnosis::belumVerifikasi()
            ->with(['kunjungan.balita', 'kader'])
            ->latest()
            ->get()
            ->map(fn($d) => [
                'id'           => $d->id,
                'nama_balita'  => $d->kunjungan->balita->nama,
                'balita_id'    => $d->kunjungan->balita_id,
                'usia_format'  => $d->kunjungan->balita->usia_format,
                'tanggal'      => $d->kunjungan->tanggal_kunjungan->format('d M Y'),
                'cf_kombinasi' => $d->cf_kombinasi,
                'cf_persen'    => $d->cf_persen,
                'status'       => $d->status_stunting,
                'label_status' => $d->label_status,
                'warna_status' => $d->warna_status,
                'kader'        => $d->kader->name,
            ]);

        $sudahVerifikasi = Diagnosis::sudahVerifikasi()
            ->with(['kunjungan.balita', 'verifikator'])
            ->latest('verified_at')
            ->take(10)
            ->get()
            ->map(fn($d) => [
                'id'           => $d->id,
                'nama_balita'  => $d->kunjungan->balita->nama,
                'tanggal'      => $d->kunjungan->tanggal_kunjungan->format('d M Y'),
                'cf_kombinasi' => $d->cf_kombinasi,
                'status_final' => $d->status_final,
                'label_status' => $d->label_status,
                'warna_status' => $d->warna_status,
                'verifikator'  => $d->verifikator?->name,
                'verified_at'  => $d->verified_at?->format('d M Y'),
            ]);

        // Statistik
        $stats = [
            'total_belum_verifikasi' => $belumVerifikasi->count(),
            'total_stunting'         => Diagnosis::stunting()->count(),
            'total_berisiko'         => Diagnosis::berisiko()->count(),
            'cf_rata_rata'           => round(
                Diagnosis::avg('cf_kombinasi') ?? 0, 2
            ),
        ];

        return Inertia::render('Bidan/Monitoring', [
            'belum_verifikasi' => $belumVerifikasi,
            'sudah_verifikasi' => $sudahVerifikasi,
            'stats'            => $stats,
        ]);
    }

    /**
     * Detail diagnosis untuk diverifikasi bidan.
     */
    public function show(Diagnosis $diagnosis): Response
    {
        $diagnosis->load([
            'kunjungan.balita.riwayat',
            'kunjungan.balita.kunjungan',
            'detail.ruleCf.gejala',
            'kader',
        ]);

        $balita    = $diagnosis->kunjungan->balita;
        $kunjungan = $diagnosis->kunjungan;
        $tren      = $this->kunjunganService->trenPertumbuhan($balita, 6);

        return Inertia::render('Bidan/Verifikasi/Show', [
            'diagnosis' => [
                'id'                => $diagnosis->id,
                'cf_kombinasi'      => $diagnosis->cf_kombinasi,
                'cf_persen'         => $diagnosis->cf_persen,
                'status_stunting'   => $diagnosis->status_stunting,
                'status_final'      => $diagnosis->status_final,
                'label_status'      => $diagnosis->label_status,
                'warna_status'      => $diagnosis->warna_status,
                'rekomendasi'       => $diagnosis->rekomendasi,
                'sudah_diverifikasi'=> $diagnosis->sudah_diverifikasi,
                'kader'             => $diagnosis->kader->name,
            ],
            'detail_gejala' => $diagnosis->detail->map(fn($d) => [
                'kode_rule'    => $d->ruleCf->kode_rule,
                'nama_gejala'  => $d->ruleCf->gejala->nama_gejala,
                'kategori'     => $d->ruleCf->gejala->kategori,
                'sumber'       => $d->ruleCf->gejala->sumber,
                'mb'           => $d->ruleCf->mb,
                'md'           => $d->ruleCf->md,
                'cf_pakar'     => $d->ruleCf->getCfPakarAttribute(),
                'gejala_aktif' => $d->gejala_aktif,
                'cf_parsial'   => $d->cf_parsial,
            ]),
            'balita' => [
                'id'            => $balita->id,
                'nama'          => $balita->nama,
                'usia_format'   => $balita->usia_format,
                'jenis_kelamin' => $balita->jenis_kelamin_lengkap,
                'nama_ibu'      => $balita->nama_ibu,
                'desa'          => $balita->desa,
            ],
            'kunjungan' => [
                'tanggal'      => $kunjungan->tanggal_kunjungan->format('d M Y'),
                'berat_badan'  => $kunjungan->berat_badan,
                'tinggi_badan' => $kunjungan->tinggi_badan,
                'lila'         => $kunjungan->lila,
                'zscore_tbu'   => $kunjungan->zscore_tbu,
                'zscore_bbu'   => $kunjungan->zscore_bbu,
                'usia_bulan'   => $kunjungan->usia_bulan,
            ],
            'tren' => $tren,
        ]);
    }

    /**
     * Simpan hasil verifikasi bidan (setuju / override / koreksi).
     */
    public function update(Request $request, Diagnosis $diagnosis)
    {
        $data = $request->validate([
            'status_override'  => 'nullable|in:normal,berisiko,stunting,stunting_berat',
            'alasan_override'  => 'nullable|string|max:500',
            'catatan_bidan'    => 'nullable|string|max:1000',
            'tindak_lanjut'    => 'nullable|in:pantau,edukasi_gizi,pmt,rujuk_puskesmas,rujuk_rsud',
            'jadwal_kontrol'   => 'nullable|date|after:today',
        ]);

        $diagnosis->update([
            'sudah_diverifikasi' => true,
            'verified_by'        => auth()->id(),
            'verified_at'        => now(),
            'status_override'    => $data['status_override'] ?? null,
            'alasan_override'    => $data['alasan_override'] ?? null,
            'catatan_bidan'      => $data['catatan_bidan'] ?? null,
            'tindak_lanjut'      => $data['tindak_lanjut'] ?? null,
            'jadwal_kontrol'     => $data['jadwal_kontrol'] ?? null,
        ]);

        return redirect()
            ->route('bidan.monitoring')
            ->with('success', 'Diagnosis berhasil diverifikasi.');
    }

    /**
     * Detail pasien untuk bidan — riwayat lengkap.
     */
    public function pasien(Balita $balita): Response
    {
        $balita->load([
            'riwayat',
            'kunjungan.diagnosis.detail.ruleCf.gejala',
        ]);

        $tren = $this->kunjunganService->trenPertumbuhan($balita);

        return Inertia::render('Bidan/Pasien/Show', [
            'balita' => [
                'id'            => $balita->id,
                'nama'          => $balita->nama,
                'usia_format'   => $balita->usia_format,
                'jenis_kelamin' => $balita->jenis_kelamin_lengkap,
                'nama_ibu'      => $balita->nama_ibu,
                'desa'          => $balita->desa,
            ],
            'riwayat_kunjungan' => $balita->kunjungan->map(fn($k) => [
                'id'           => $k->id,
                'tanggal'      => $k->tanggal_kunjungan->format('d M Y'),
                'usia_bulan'   => $k->usia_bulan,
                'berat_badan'  => $k->berat_badan,
                'tinggi_badan' => $k->tinggi_badan,
                'zscore_tbu'   => $k->zscore_tbu,
                'status'       => $k->status_zscore_tbu,
                'warna'        => $k->warna_status,
                'diagnosis'    => $k->diagnosis ? [
                    'id'           => $k->diagnosis->id,
                    'cf_persen'    => $k->diagnosis->cf_persen,
                    'status_final' => $k->diagnosis->status_final,
                    'label_status' => $k->diagnosis->label_status,
                    'tindak_lanjut'=> $k->diagnosis->tindak_lanjut,
                    'verified'     => $k->diagnosis->sudah_diverifikasi,
                ] : null,
            ]),
            'tren' => $tren,
        ]);
    }
}
