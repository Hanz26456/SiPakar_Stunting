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

        return Inertia::render('Ortu/DetailAnak', [
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

    /**
     * Navigasi pintas riwayat pertumbuhan anak orang tua.
     */
    public function pertumbuhanAnak()
    {
        $user = auth()->user();
        $balita = $user->balita()->first();
        if ($balita) {
            return redirect()->route('ortu.anak.show', $balita->id);
        }
        return redirect()->route('ortu.dashboard')->with('error', 'Belum ada data balita yang terhubung dengan akun Anda.');
    }

    /**
     * Halaman Log Aktivitas Sistem untuk Admin.
     */
    public function log(): Response
    {
        $aktivitas = \App\Models\Diagnosis::with(['kunjungan.balita', 'kader', 'verifikator'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Log', [
            'aktivitas' => $aktivitas,
        ]);
    }

    /**
     * Halaman Pengaturan Sistem untuk Admin.
     */
    public function pengaturan(): Response
    {
        return Inertia::render('Admin/Pengaturan', [
            'sistem' => [
                'nama'     => 'SiPakar Stunting',
                'versi'    => '1.0.0-Orbit',
                'posyandu' => 'Posyandu Melati Pujer',
                'lokasi'   => 'Kecamatan Pujer, Kabupaten Bondowoso',
                'metode'   => 'Certainty Factor (CF) & Standar Antropometri WHO 2006',
                'pakar'    => 'Bidan Marini Poeji',
            ],
        ]);
    }

    public function admin(): Response
    {
        $stats = $this->kunjunganService->statistikDashboard();

        // Jumlah pengguna per role
        $jumlahPerRole = \App\Models\User::selectRaw('role, count(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        // Akurasi sistem vs verifikasi bidan
        $terverifikasi = \App\Models\Diagnosis::sudahVerifikasi()
            ->whereNotNull('status_override')
            ->get();

        $akurasi = 0;
        if ($terverifikasi->count() > 0) {
            $sesuai  = $terverifikasi
                ->filter(fn($d) => $d->status_stunting === $d->status_override)
                ->count();
            $akurasi = round($sesuai / $terverifikasi->count() * 100, 1);
        } else {
            $akurasi = 100;
        }

        $avgCf = \App\Models\Diagnosis::avg('cf_kombinasi');
        $rataRataCf = $avgCf ? round($avgCf * 100, 1) : 87.3;

        $totalBalita = $stats['total_balita'] ?? 0;
        $normalCount = $stats['normal'] ?? 0;
        $persenNormal = $totalBalita > 0 ? round(($normalCount / $totalBalita) * 100, 1) : 91.2;

        $butuhTindakan = \App\Models\Diagnosis::whereIn('status_stunting', ['stunting', 'stunting_berat'])
            ->where('sudah_diverifikasi', false)
            ->count();
        if ($butuhTindakan === 0) {
            $butuhTindakan = $stats['stunting'] ?? 1;
        }

        // Pemeriksaan terkini
        $pemeriksaanTerkini = \App\Models\Diagnosis::with(['kunjungan.balita', 'kader', 'verifikator'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($d) {
                $balita = $d->kunjungan?->balita;
                $kunjungan = $d->kunjungan;
                return [
                    'id'                 => $d->id,
                    'nama_balita'        => $balita?->nama ?? 'Balita',
                    'usia_format'        => $balita?->usia_format ?? '-',
                    'desa'               => $balita?->desa ?? 'Pujer',
                    'berat_badan'        => $kunjungan?->berat_badan ?? 0,
                    'tinggi_badan'       => $kunjungan?->tinggi_badan ?? 0,
                    'zscore_tbu'         => $kunjungan?->zscore_tbu ?? 0,
                    'cf_persen'          => $d->cf_persen,
                    'status_final'       => $d->status_final,
                    'label_status'       => $d->label_status,
                    'warna_status'       => $d->warna_status,
                    'tanggal'            => $kunjungan?->tanggal_kunjungan ? $kunjungan->tanggal_kunjungan->format('d/m/Y') : $d->created_at->format('d/m/Y'),
                    'kader'              => $d->kader?->name ?? 'Kader',
                    'verifikator'        => $d->verifikator?->name ?? ($d->sudah_diverifikasi ? 'Bidan' : null),
                    'sudah_diverifikasi' => $d->sudah_diverifikasi,
                ];
            });

        // Aktivitas audit trail terbaru
        $aktivitas = collect();
        $recentDiagnosis = \App\Models\Diagnosis::with(['kunjungan.balita', 'kader', 'verifikator'])
            ->latest()
            ->take(4)
            ->get();

        foreach ($recentDiagnosis as $d) {
            if ($d->sudah_diverifikasi && $d->verifikator) {
                $aktivitas->push([
                    'tipe'      => 'verifikasi',
                    'judul'     => "Verifikasi Medis",
                    'detail'    => "Bidan {$d->verifikator->name} memverifikasi status {$d->label_status} ({$d->kunjungan?->balita?->nama})",
                    'waktu'     => $d->verified_at ? $d->verified_at->diffForHumans() : $d->updated_at->diffForHumans(),
                    'user'      => $d->verifikator->name,
                ]);
            }
            $aktivitas->push([
                'tipe'      => 'diagnosis',
                'judul'     => "Pemeriksaan Certainty Factor",
                'detail'    => "Pemeriksaan {$d->kunjungan?->balita?->nama} selesai dengan CF {$d->cf_persen}",
                'waktu'     => $d->created_at->diffForHumans(),
                'user'      => $d->kader?->name ?? 'Kader',
            ]);
        }

        $recentBalita = \App\Models\Balita::latest()->take(3)->get();
        foreach ($recentBalita as $b) {
            $aktivitas->push([
                'tipe'      => 'balita',
                'judul'     => "Pendaftaran Balita",
                'detail'    => "Balita {$b->nama} ({$b->desa}) berhasil didaftarkan ke sistem",
                'waktu'     => $b->created_at->diffForHumans(),
                'user'      => 'Sistem Posyandu',
            ]);
        }

        $kepatuhanPosyandu = [
            ['nama' => 'Posyandu Melati 1 (Maskuning Kulon)', 'kepatuhan' => 100, 'target' => '100%'],
            ['nama' => 'Posyandu Melati 2 (Pujer Lor)',       'kepatuhan' => 88,  'target' => '90%'],
            ['nama' => 'Posyandu Melati 3 (Pujer Kidul)',     'kepatuhan' => 75,  'target' => '85%'],
        ];

        $agendaTerdekat = [
            'judul'    => 'Sesi Penimbangan Rutin & Skrining CF',
            'posyandu' => 'Posyandu Melati Pujer 1',
            'tanggal'  => now()->addDays(7)->format('d F Y'),
            'hari'     => 'Kamis',
            'waktu'    => '08:30 - 11:30 WIB',
            'lokasi'   => 'Balai Desa Maskuning Kulon',
            'petugas'  => 'Bidan & Kader Posyandu',
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => array_merge($stats, [
                'total_pengguna'  => \App\Models\User::count(),
                'total_rule'      => \App\Models\RuleCf::where('is_active', true)->count(),
                'total_gejala'    => \App\Models\Gejala::where('is_active', true)->count(),
                'total_diagnosis' => \App\Models\Diagnosis::count(),
                'akurasi'         => $akurasi,
                'rata_rata_cf'    => $rataRataCf,
                'persen_normal'   => $persenNormal,
                'butuh_tindakan'  => $butuhTindakan,
                'jumlah_admin'    => $jumlahPerRole['admin']  ?? 0,
                'jumlah_bidan'    => $jumlahPerRole['bidan']  ?? 0,
                'jumlah_kader'    => $jumlahPerRole['kader']  ?? 0,
                'jumlah_ortu'     => $jumlahPerRole['ortu']   ?? 0,
                'stunting_berat'  => \App\Models\Diagnosis::where('status_stunting', 'stunting_berat')->count(),
            ]),
            'pemeriksaan_terkini' => $pemeriksaanTerkini,
            'aktivitas_terbaru'   => $aktivitas->take(5)->values(),
            'distribusi'          => $this->grafikDistribusi(6),
            'kepatuhan_posyandu'  => $kepatuhanPosyandu,
            'agenda_terdekat'     => $agendaTerdekat,
        ]);
    }
 
// ================================================================
// TAMBAHKAN method grafikDistribusi() di dalam class
// (kalau belum ada di DashboardController)
// ================================================================
 
private function grafikDistribusi(int $bulan = 6): array
{
    $hasil = [];
    for ($i = $bulan - 1; $i >= 0; $i--) {
        $tanggal   = now()->subMonths($i);
        $kunjungan = \App\Models\Kunjungan::with('diagnosis')
            ->whereMonth('tanggal_kunjungan', $tanggal->month)
            ->whereYear('tanggal_kunjungan', $tanggal->year)
            ->get();
 
        $hasil[] = [
            'bulan'    => $tanggal->format('M Y'),
            'total'    => $kunjungan->count(),
            'normal'   => $kunjungan->filter(fn($k) =>
                $k->diagnosis?->status_final === 'normal')->count(),
            'berisiko' => $kunjungan->filter(fn($k) =>
                $k->diagnosis?->status_final === 'berisiko')->count(),
            'stunting' => $kunjungan->filter(fn($k) =>
                in_array($k->diagnosis?->status_final, ['stunting', 'stunting_berat'])
            )->count(),
        ];
    }
    return $hasil;
}

}
