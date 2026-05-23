<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\Diagnosis;
use App\Models\Kunjungan;
use App\Services\KunjunganService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly KunjunganService $kunjunganService
    ) {}

    // ===== KADER =====
    public function kader(): Response
    {
        $stats = $this->kunjunganService->statistikDashboard();

        $balitaPerhatian = Diagnosis::with(['kunjungan.balita'])
            ->whereIn('status_stunting', ['stunting', 'stunting_berat'])
            ->where('sudah_diverifikasi', false)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($d) => [
                'id'           => $d->id,
                'nama_balita'  => $d->kunjungan->balita->nama,
                'balita_id'    => $d->kunjungan->balita_id,
                'usia_format'  => $d->kunjungan->balita->usia_format,
                'cf_persen'    => $d->cf_persen,
                'label_status' => $d->label_status,
                'warna_status' => $d->warna_status,
                'tanggal'      => $d->kunjungan->tanggal_kunjungan->format('d M Y'),
            ]);

        $kunjunganBulanIni = Kunjungan::bulanIni()
            ->with(['balita', 'diagnosis'])
            ->latest('tanggal_kunjungan')
            ->take(5)
            ->get()
            ->map(fn($k) => [
                'id'            => $k->id,
                'nama_balita'   => $k->balita->nama,
                'tanggal'       => $k->tanggal_kunjungan->format('d M Y'),
                'berat_badan'   => $k->berat_badan,
                'tinggi_badan'  => $k->tinggi_badan,
                'status'        => $k->status_zscore_tbu,
                'warna'         => $k->warna_status,
                'sudah_diagnosa'=> $k->diagnosis !== null,
            ]);

        // Data grafik distribusi 6 bulan terakhir
        $distribusi = $this->grafikDistribusi(6);

        return Inertia::render('Kader/Dashboard', [
            'stats'              => $stats,
            'balita_perhatian'   => $balitaPerhatian,
            'kunjungan_terbaru'  => $kunjunganBulanIni,
            'distribusi'         => $distribusi,
        ]);
    }

    // ===== BIDAN =====
    public function bidan(): Response
    {
        $belumVerifikasi = Diagnosis::belumVerifikasi()
            ->with(['kunjungan.balita', 'kader'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($d) => [
                'id'           => $d->id,
                'nama_balita'  => $d->kunjungan->balita->nama,
                'balita_id'    => $d->kunjungan->balita_id,
                'usia_format'  => $d->kunjungan->balita->usia_format,
                'cf_persen'    => $d->cf_persen,
                'label_status' => $d->label_status,
                'warna_status' => $d->warna_status,
                'kader'        => $d->kader->name,
                'tanggal'      => $d->kunjungan->tanggal_kunjungan->format('d M Y H:i'),
            ]);

        $penangananAktif = Diagnosis::sudahVerifikasi()
            ->whereIn('status_stunting', ['stunting', 'stunting_berat', 'berisiko'])
            ->with(['kunjungan.balita'])
            ->latest('verified_at')
            ->take(8)
            ->get()
            ->map(fn($d) => [
                'id'            => $d->id,
                'nama_balita'   => $d->kunjungan->balita->nama,
                'balita_id'     => $d->kunjungan->balita_id,
                'status_final'  => $d->status_final,
                'label_status'  => $d->label_status,
                'warna_status'  => $d->warna_status,
                'tindak_lanjut' => $d->tindak_lanjut,
                'jadwal_kontrol'=> $d->jadwal_kontrol?->format('d M Y'),
            ]);

        return Inertia::render('Bidan/Dashboard', [
            'stats' => [
                'perlu_verifikasi' => Diagnosis::belumVerifikasi()->count(),
                'stunting_aktif'   => Diagnosis::stunting()->count(),
                'sudah_dirujuk'    => Diagnosis::where('tindak_lanjut', 'rujuk_puskesmas')->count(),
                'cf_rata_rata'     => round(Diagnosis::avg('cf_kombinasi') ?? 0, 2),
            ],
            'belum_verifikasi'  => $belumVerifikasi,
            'penanganan_aktif'  => $penangananAktif,
            'distribusi'        => $this->grafikDistribusi(6),
        ]);
    }

    // ===== ORANG TUA =====
    public function ortu(): Response
    {
        $user   = auth()->user();
        $balita = $user->balita()->with([
            'kunjunganTerbaru',
            'diagnosisTerbaru',
        ])->get();

        return Inertia::render('Ortu/Dashboard', [
            'balita' => $balita->map(fn($b) => [
                'id'            => $b->id,
                'nama'          => $b->nama,
                'usia_format'   => $b->usia_format,
                'jenis_kelamin' => $b->jenis_kelamin_lengkap,
                'status_terbaru'=> $b->status_terbaru,
                'label_status'  => $b->diagnosisTerbaru?->label_status ?? 'Belum ada data',
                'warna_status'  => $b->diagnosisTerbaru?->warna_status ?? 'gray',
                'cf_persen'     => $b->diagnosisTerbaru?->cf_persen,
                'berat_terbaru' => $b->kunjunganTerbaru?->berat_badan,
                'tinggi_terbaru'=> $b->kunjunganTerbaru?->tinggi_badan,
                'zscore_tbu'    => $b->kunjunganTerbaru?->zscore_tbu,
                'kunjungan_terakhir' => $b->kunjunganTerbaru
                    ?->tanggal_kunjungan->format('d M Y'),
            ]),
        ]);
    }

    /**
     * Detail anak untuk orang tua.
     */
    public function detailAnak(Balita $balita): Response
    {
        // Pastikan balita ini milik user yang login
        abort_if(
            $balita->user_id !== auth()->id(),
            403,
            'Anda tidak memiliki akses ke data ini.'
        );

        $balita->load(['riwayat', 'kunjungan.diagnosis']);
        $tren = $this->kunjunganService->trenPertumbuhan($balita);

        $diagnosisTerbaru = $balita->diagnosisTerbaru;

        return Inertia::render('Ortu/DetilAnak', [
            'balita' => [
                'id'            => $balita->id,
                'nama'          => $balita->nama,
                'usia_format'   => $balita->usia_format,
                'jenis_kelamin' => $balita->jenis_kelamin_lengkap,
            ],
            'status' => $diagnosisTerbaru ? [
                'cf_persen'     => $diagnosisTerbaru->cf_persen,
                'label_status'  => $diagnosisTerbaru->label_status,
                'warna_status'  => $diagnosisTerbaru->warna_status,
                'rekomendasi'   => $diagnosisTerbaru->rekomendasi,
                'tindak_lanjut' => $diagnosisTerbaru->tindak_lanjut,
                'jadwal_kontrol'=> $diagnosisTerbaru->jadwal_kontrol?->format('d M Y'),
                'verified'      => $diagnosisTerbaru->sudah_diverifikasi,
            ] : null,
            'pengukuran_terbaru' => $balita->kunjunganTerbaru ? [
                'berat_badan'  => $balita->kunjunganTerbaru->berat_badan,
                'tinggi_badan' => $balita->kunjunganTerbaru->tinggi_badan,
                'zscore_tbu'   => $balita->kunjunganTerbaru->zscore_tbu,
                'tanggal'      => $balita->kunjunganTerbaru->tanggal_kunjungan->format('d M Y'),
            ] : null,
            'tren' => $tren,
        ]);
    }

    /**
     * Halaman panduan gizi untuk orang tua.
     */
    public function panduan(): Response
    {
        return Inertia::render('Ortu/Panduan');
    }

    // ===== ADMIN =====
    public function admin(): Response
    {
        $stats = $this->kunjunganService->statistikDashboard();

        return Inertia::render('Admin/Dashboard', [
            'stats' => array_merge($stats, [
                'total_pengguna' => \App\Models\User::count(),
                'total_rule'     => \App\Models\RuleCf::where('is_active', true)->count(),
                'total_diagnosis'=> Diagnosis::count(),
                'akurasi'        => $this->hitungAkurasi(),
            ]),
            'aktivitas_terbaru' => $this->aktivitasTerbaru(),
            'distribusi'        => $this->grafikDistribusi(6),
        ]);
    }

    public function log(): Response
    {
        // Bisa dikembangkan dengan package spatie/laravel-activitylog
        return Inertia::render('Admin/Log');
    }

    public function pengaturan(): Response
    {
        return Inertia::render('Admin/Pengaturan');
    }

    // ===== PRIVATE HELPERS =====

    /**
     * Data grafik distribusi status stunting N bulan terakhir.
     */
    private function grafikDistribusi(int $bulan = 6): array
    {
        $hasil = [];
        for ($i = $bulan - 1; $i >= 0; $i--) {
            $tanggal = now()->subMonths($i);
            $label   = $tanggal->format('M Y');

            $kunjungan = Kunjungan::whereMonth('tanggal_kunjungan', $tanggal->month)
                ->whereYear('tanggal_kunjungan', $tanggal->year)
                ->with('diagnosis')
                ->get();

            $hasil[] = [
                'bulan'          => $label,
                'total'          => $kunjungan->count(),
                'normal'         => $kunjungan->filter(fn($k) =>
                    $k->diagnosis?->status_final === 'normal')->count(),
                'berisiko'       => $kunjungan->filter(fn($k) =>
                    $k->diagnosis?->status_final === 'berisiko')->count(),
                'stunting'       => $kunjungan->filter(fn($k) =>
                    in_array($k->diagnosis?->status_final, ['stunting', 'stunting_berat'])
                )->count(),
            ];
        }
        return $hasil;
    }

    /**
     * Hitung akurasi sistem vs verifikasi bidan.
     * CF akurat = status sistem sama dengan status setelah verifikasi bidan.
     */
    private function hitungAkurasi(): float
    {
        $terverifikasi = Diagnosis::sudahVerifikasi()
            ->whereNotNull('status_override')
            ->get();

        if ($terverifikasi->isEmpty()) return 0.0;

        $sesuai = $terverifikasi->filter(
            fn($d) => $d->status_stunting === $d->status_override
        )->count();

        return round($sesuai / $terverifikasi->count() * 100, 1);
    }

    /**
     * Aktivitas terbaru untuk dashboard admin.
     */
    private function aktivitasTerbaru(): array
    {
        $diagnosis = Diagnosis::with(['kunjungan.balita', 'kader'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($d) => [
                'tipe'    => 'diagnosis',
                'label'   => "Diagnosis {$d->kunjungan->balita->nama} — {$d->label_status}",
                'user'    => $d->kader->name,
                'waktu'   => $d->created_at->diffForHumans(),
            ]);

        return $diagnosis->toArray();
    }
}
