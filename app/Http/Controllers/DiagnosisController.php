<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\Kunjungan;
use App\Models\Diagnosis;
use App\Models\Gejala;
use App\Services\CertaintyFactorService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DiagnosisController extends Controller
{
    public function __construct(
        private readonly CertaintyFactorService $cfService
    ) {}

    /**
     * Halaman form diagnosis — tampilkan gejala manual untuk diceklis kader.
     * Gejala otomatis sudah terdeteksi sistem, tinggal gejala klinis yang manual.
     */
    public function create(Kunjungan $kunjungan): Response
    {
        $kunjungan->load(['balita.riwayat', 'balita.kunjungan']);
        $balita = $kunjungan->balita;

        // Ambil gejala manual saja (yang perlu diceklis kader)
        $gejalaManu = Gejala::aktif()
            ->where('sumber', 'manual')
            ->get()
            ->map(fn($g) => [
                'id'          => $g->id,
                'kode'        => $g->kode,
                'nama_gejala' => $g->nama_gejala,
                'kategori'    => $g->kategori,
                'deskripsi'   => $g->deskripsi,
            ]);

        // Preview gejala otomatis yang sudah terdeteksi
        $gejalaTerdeteksi = $this->cfService->identifikasiGejala($kunjungan);

        // Ambil semua gejala untuk preview
        $semuaGejala = Gejala::aktif()->with('rulesAktif')->get()->map(fn($g) => [
            'id'          => $g->id,
            'kode'        => $g->kode,
            'nama_gejala' => $g->nama_gejala,
            'kategori'    => $g->kategori,
            'sumber'      => $g->sumber,
            'terdeteksi'  => $gejalaTerdeteksi[$g->id] ?? false,
        ]);

        return Inertia::render('Kader/Diagnosis/Create', [
            'kunjungan' => [
                'id'              => $kunjungan->id,
                'tanggal'         => $kunjungan->tanggal_kunjungan->format('d M Y'),
                'berat_badan'     => $kunjungan->berat_badan,
                'tinggi_badan'    => $kunjungan->tinggi_badan,
                'lila'            => $kunjungan->lila,
                'zscore_tbu'      => $kunjungan->zscore_tbu,
                'zscore_bbu'      => $kunjungan->zscore_bbu,
                'usia_bulan'      => $kunjungan->usia_bulan,
                'status_zscore'   => $kunjungan->status_zscore_tbu,
            ],
            'balita' => [
                'id'           => $balita->id,
                'nama'         => $balita->nama,
                'usia_format'  => $balita->usia_format,
                'jenis_kelamin'=> $balita->jenis_kelamin_lengkap,
            ],
            'gejala_manual'    => $gejalaManu,
            'semua_gejala'     => $semuaGejala,
            'gejala_terdeteksi'=> $gejalaTerdeteksi,
        ]);
    }

    /**
     * Preview hasil CF secara real-time (AJAX dari Vue).
     * Dipanggil saat kader mencentang/uncentang gejala.
     */
    public function preview(Request $request, Kunjungan $kunjungan)
    {
        $kunjungan->load(['balita.riwayat', 'balita.kunjungan']);

        $gejalaManu = $request->validate([
            'gejala' => 'array',
            'gejala.*' => 'boolean',
        ])['gejala'] ?? [];

        $hasil = $this->cfService->simulasi($kunjungan, $gejalaManu);

        return response()->json($hasil);
    }

    /**
     * Simpan hasil diagnosis ke database.
     */
    public function store(Request $request, Kunjungan $kunjungan)
    {
        $request->validate([
            'gejala'   => 'array',
            'gejala.*' => 'boolean',
        ]);

        $kunjungan->load(['balita.riwayat', 'balita.kunjungan']);

        $diagnosis = $this->cfService->diagnosa(
            $kunjungan,
            $request->get('gejala', [])
        );

        return redirect()
            ->route('kader.diagnosis.show', $diagnosis)
            ->with('success', 'Diagnosis berhasil disimpan.');
    }

    /**
     * Halaman hasil diagnosis.
     */
    public function show(Diagnosis $diagnosis): Response
    {
        $diagnosis->load([
            'kunjungan.balita.riwayat',
            'kunjungan.balita.kunjungan',
            'detail.ruleCf.gejala',
            'verifikator',
        ]);

        $balita    = $diagnosis->kunjungan->balita;
        $kunjungan = $diagnosis->kunjungan;

        return Inertia::render('Kader/Diagnosis/Show', [
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
                'status_override'   => $diagnosis->status_override,
                'catatan_bidan'     => $diagnosis->catatan_bidan,
                'tindak_lanjut'     => $diagnosis->tindak_lanjut,
                'jadwal_kontrol'    => $diagnosis->jadwal_kontrol?->format('d M Y'),
                'verifikator'       => $diagnosis->verifikator?->name,
                'verified_at'       => $diagnosis->verified_at?->format('d M Y H:i'),
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
                'id'          => $balita->id,
                'nama'        => $balita->nama,
                'usia_format' => $balita->usia_format,
            ],
            'kunjungan' => [
                'tanggal'      => $kunjungan->tanggal_kunjungan->format('d M Y'),
                'berat_badan'  => $kunjungan->berat_badan,
                'tinggi_badan' => $kunjungan->tinggi_badan,
                'lila'         => $kunjungan->lila,
                'zscore_tbu'   => $kunjungan->zscore_tbu,
                'zscore_bbu'   => $kunjungan->zscore_bbu,
            ],
        ]);
    }
}
