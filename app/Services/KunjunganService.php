<?php

namespace App\Services;

use App\Models\Balita;
use App\Models\Kunjungan;
use Carbon\Carbon;

/**
 * KunjunganService
 * Mengelola pencatatan kunjungan posyandu.
 * Hitung z-score otomatis saat data disimpan.
 */
class KunjunganService
{
    public function __construct(
        private readonly ZScoreService $zscoreService
    ) {}

    /**
     * Simpan kunjungan baru + hitung z-score otomatis.
     */
    public function simpan(Balita $balita, array $data): Kunjungan
    {
        $tanggal    = Carbon::parse($data['tanggal_kunjungan']);
        $usiaBulan  = (int) Carbon::parse($balita->tanggal_lahir)
                            ->diffInMonths($tanggal);

        // Hitung z-score otomatis
        $zscoreTBU = $this->zscoreService->hitungZscoreTBU(
            $usiaBulan,
            $balita->jenis_kelamin,
            $data['tinggi_badan']
        );

        $zscoreBBU = $this->zscoreService->hitungZscoreBBU(
            $usiaBulan,
            $balita->jenis_kelamin,
            $data['berat_badan']
        );

        return Kunjungan::create([
            'balita_id'          => $balita->id,
            'kader_id'           => auth()->id(),
            'tanggal_kunjungan'  => $data['tanggal_kunjungan'],
            'berat_badan'        => $data['berat_badan'],
            'tinggi_badan'       => $data['tinggi_badan'],
            'lila'               => $data['lila'] ?? null,
            'lingkar_kepala'     => $data['lingkar_kepala'] ?? null,
            'usia_bulan'         => $usiaBulan,
            'zscore_tbu'         => $zscoreTBU,
            'zscore_bbu'         => $zscoreBBU,
            'catatan'            => $data['catatan'] ?? null,
        ]);
    }

    /**
     * Ambil data tren pertumbuhan untuk grafik Vue.
     */
    public function trenPertumbuhan(Balita $balita, int $bulan = 12): array
    {
        $kunjungan = $balita->kunjungan()
            ->orderBy('tanggal_kunjungan', 'asc')
            ->take($bulan)
            ->get();

        return $kunjungan->map(fn($k) => [
            'bulan'          => $k->tanggal_kunjungan->format('M Y'),
            'berat_badan'    => $k->berat_badan,
            'tinggi_badan'   => $k->tinggi_badan,
            'lila'           => $k->lila,
            'zscore_tbu'     => $k->zscore_tbu,
            'zscore_bbu'     => $k->zscore_bbu,
            'usia_bulan'     => $k->usia_bulan,
            'status'         => $k->status_zscore_tbu,
        ])->toArray();
    }

    /**
     * Ringkasan statistik balita untuk dashboard.
     */
    public function statistikDashboard(): array
    {
        $total    = Balita::aktif()->count();
        $stunting = \App\Models\Diagnosis::stunting()->count();
        $berisiko = \App\Models\Diagnosis::berisiko()->count();
        $normal   = $total - $stunting - $berisiko;

        return [
            'total_balita'    => $total,
            'stunting'        => $stunting,
            'berisiko'        => $berisiko,
            'normal'          => max(0, $normal),
            'persen_stunting' => $total > 0
                ? round($stunting / $total * 100, 1)
                : 0,
        ];
    }
}