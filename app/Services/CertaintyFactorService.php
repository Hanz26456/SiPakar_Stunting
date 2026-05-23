<?php

namespace App\Services;

use App\Models\Balita;
use App\Models\Kunjungan;
use App\Models\Diagnosis;
use App\Models\DetailDiagnosis;
use App\Models\RuleCf;
use App\Models\Gejala;
use Illuminate\Support\Facades\DB;

/**
 * CertaintyFactorService
 *
 * Inti algoritma sistem pakar diagnosis stunting.
 *
 * Alur proses:
 * 1. Identifikasi gejala — otomatis dari data kunjungan + riwayat
 * 2. Cocokkan dengan rule CF aktif
 * 3. Hitung CF parsial tiap rule
 * 4. Kombinasikan semua CF
 * 5. Tentukan status stunting
 * 6. Simpan hasil ke database
 */
class CertaintyFactorService
{
    public function __construct(
        private readonly ZScoreService $zscoreService
    ) {}

    /**
     * Jalankan diagnosis CF untuk satu kunjungan.
     * Dipanggil dari DiagnosisController.
     *
     * @param Kunjungan $kunjungan     Data kunjungan yang sudah tersimpan
     * @param array     $gejalaManu    Gejala manual dari input kader [gejala_id => bool]
     * @return Diagnosis
     */
    public function diagnosa(
        Kunjungan $kunjungan,
        array $gejalaManu = []
    ): Diagnosis {
        // Load relasi yang dibutuhkan
        $kunjungan->load(['balita.riwayat', 'balita.kunjungan']);

        // 1. Identifikasi semua gejala (otomatis + manual)
        $gejalaTerdeteksi = $this->identifikasiGejala($kunjungan, $gejalaManu);

        // 2. Ambil semua rule CF aktif beserta gejalanya
        $rules = RuleCf::aktif()
            ->with('gejala')
            ->get();

        // 3. Hitung CF tiap rule dan kombinasikan
        $hasilCF = $this->hitungCFKombinasi($rules, $gejalaTerdeteksi);

        // 4. Tentukan status stunting
        $statusStunting = $this->tentukanStatus($hasilCF['cf_kombinasi']);

        // 5. Generate rekomendasi
        $rekomendasi = $this->generateRekomendasi(
            $statusStunting,
            $kunjungan->balita,
            $hasilCF['cf_kombinasi']
        );

        // 6. Simpan ke database dalam satu transaksi
        return DB::transaction(function () use (
            $kunjungan, $hasilCF, $statusStunting,
            $rekomendasi, $gejalaTerdeteksi, $rules
        ) {
            // Hapus diagnosis lama kalau ada
            Diagnosis::where('kunjungan_id', $kunjungan->id)->delete();

            // Simpan diagnosis
            $diagnosis = Diagnosis::create([
                'kunjungan_id'    => $kunjungan->id,
                'kader_id'        => auth()->id(),
                'cf_kombinasi'    => $hasilCF['cf_kombinasi'],
                'status_stunting' => $statusStunting,
                'rekomendasi'     => $rekomendasi,
            ]);

            // Simpan detail tiap rule
            foreach ($rules as $rule) {
                $aktif    = $gejalaTerdeteksi[$rule->gejala_id] ?? false;
                $cfParsial = $aktif ? $hasilCF['cf_per_rule'][$rule->id] ?? null : null;

                DetailDiagnosis::create([
                    'diagnosis_id' => $diagnosis->id,
                    'rule_cf_id'   => $rule->id,
                    'gejala_aktif' => $aktif,
                    'cf_parsial'   => $cfParsial,
                ]);
            }

            return $diagnosis;
        });
    }

    /**
     * Identifikasi gejala yang aktif pada balita.
     * Gabungan deteksi otomatis dari data + input manual kader.
     *
     * @return array [gejala_id => bool]
     */
    public function identifikasiGejala(
        Kunjungan $kunjungan,
        array $gejalaManu = []
    ): array {
        $balita  = $kunjungan->balita;
        $riwayat = $balita->riwayat;
        $hasil   = [];

        // Ambil semua gejala aktif
        $semuaGejala = Gejala::aktif()->get();

        foreach ($semuaGejala as $gejala) {

            if ($gejala->sumber === 'otomatis') {
                // Deteksi otomatis dari data kunjungan / riwayat
                $hasil[$gejala->id] = $this->cekGejalaOtomatis(
                    $gejala,
                    $kunjungan,
                    $riwayat,
                    $balita
                );
            } else {
                // Gejala manual — dari input kader
                $hasil[$gejala->id] = (bool) ($gejalaManu[$gejala->id] ?? false);
            }
        }

        return $hasil;
    }

    /**
     * Cek apakah gejala otomatis aktif berdasarkan data kunjungan.
     * INI ADALAH BAGIAN NOVELTY UTAMA SKRIPSI:
     * Sistem mengidentifikasi gejala dari DATA berkelanjutan, bukan input manual.
     */
    private function cekGejalaOtomatis(
        Gejala $gejala,
        Kunjungan $kunjungan,
        $riwayat,
        Balita $balita
    ): bool {
        $kolom    = $gejala->kolom_sumber;
        $operator = $gejala->operator;
        $threshold = $gejala->nilai_threshold;

        // Gejala berbasis data kunjungan (antropometri)
        if (in_array($kolom, ['zscore_tbu', 'zscore_bbu', 'zscore_bbtb', 'lila', 'lingkar_kepala'])) {
            $nilai = $kunjungan->$kolom;
            if ($nilai === null) return false;
            return $this->bandingkan($nilai, $operator, $threshold);
        }

        // Gejala BB tidak naik — cek tren dari riwayat kunjungan
        if ($kolom === 'berat_badan' && $operator === 'trend_flat') {
            return $this->cekBBTidakNaik($balita, $kunjungan, (int) $threshold);
        }

        // Gejala dari data riwayat balita
        if ($riwayat && in_array($kolom, [
            'asi_eksklusif', 'mpasi_sesuai_usia', 'infeksi_berulang'
        ])) {
            $nilai = $riwayat->$kolom;

            // Untuk boolean: threshold 0 = false, 1 = true
            if ($operator === '=') {
                return ((int) $nilai) === ((int) $threshold);
            }
        }

        return false;
    }

    /**
     * Deteksi apakah BB balita tidak naik selama N bulan berturut.
     * Ini fitur integrasi data berkelanjutan — jantung novelty penelitian ini.
     */
    private function cekBBTidakNaik(
        Balita $balita,
        Kunjungan $kunjunganSekarang,
        int $jumlahBulan = 2
    ): bool {
        // Ambil N kunjungan terakhir sebelum kunjungan ini
        $kunjunganLalu = $balita->kunjungan()
            ->where('tanggal_kunjungan', '<', $kunjunganSekarang->tanggal_kunjungan)
            ->orderBy('tanggal_kunjungan', 'desc')
            ->take($jumlahBulan)
            ->pluck('berat_badan')
            ->toArray();

        // Tidak cukup data historis
        if (count($kunjunganLalu) < $jumlahBulan) return false;

        $bbSekarang = $kunjunganSekarang->berat_badan;

        // Cek apakah BB tidak naik dibanding semua kunjungan dalam rentang
        foreach ($kunjunganLalu as $bbLalu) {
            // Kalau ada kunjungan lalu yang BB-nya lebih rendah dari sekarang
            // berarti BB pernah naik — TIDAK memenuhi gejala ini
            if ($bbSekarang > $bbLalu) return false;
        }

        // BB sekarang <= semua BB dalam rentang = tidak naik
        return true;
    }

    /**
     * Hitung CF kombinasi dari semua rule yang aktif.
     *
     * Rumus kombinasi CF:
     *   CF_kombinasi = CF1 + CF2 * (1 - CF1)
     *   (diulang untuk setiap rule baru)
     */
    private function hitungCFKombinasi(
        $rules,
        array $gejalaTerdeteksi
    ): array {
        $cfKombinasi = 0.0;
        $cfPerRule   = [];

        foreach ($rules as $rule) {
            $gejalaAktif = $gejalaTerdeteksi[$rule->gejala_id] ?? false;

            if (!$gejalaAktif) continue;

            // CF rule ini = CF pakar (MB - MD)
            $cfRule = $rule->getCfPakarAttribute();

            // Simpan CF parsial rule ini
            $cfPerRule[$rule->id] = $cfRule;

            // Kombinasikan dengan CF sebelumnya
            if ($cfKombinasi == 0) {
                $cfKombinasi = $cfRule;
            } else {
                // Rumus kombinasi CF
                $cfKombinasi = $cfKombinasi + ($cfRule * (1 - $cfKombinasi));
            }
        }

        return [
            'cf_kombinasi' => round(min(1.0, max(0.0, $cfKombinasi)), 4),
            'cf_per_rule'  => $cfPerRule,
        ];
    }

    /**
     * Tentukan status stunting berdasarkan nilai CF kombinasi.
     * Ambang batas bisa disesuaikan dari hasil wawancara pakar.
     */
    public function tentukanStatus(float $cfKombinasi): string
    {
        // Cek z-score TB/U juga sebagai penentu utama (standar WHO)
        if ($cfKombinasi >= 0.90) return 'stunting_berat';
        if ($cfKombinasi >= 0.70) return 'stunting';
        if ($cfKombinasi >= 0.40) return 'berisiko';
        return 'normal';
    }

    /**
     * Generate rekomendasi tindak lanjut berdasarkan status.
     */
    public function generateRekomendasi(
        string $status,
        Balita $balita,
        float $cf
    ): string {
        $nama  = $balita->nama;
        $usia  = $balita->usia_format;

        return match($status) {
            'stunting_berat' =>
                "Balita {$nama} (usia {$usia}) terdeteksi mengalami stunting berat " .
                "dengan nilai keyakinan " . round($cf * 100, 1) . "%. " .
                "Segera rujuk ke Puskesmas atau fasilitas kesehatan terdekat. " .
                "Berikan Pemberian Makanan Tambahan (PMT) dan pantau ketat setiap 2 minggu. " .
                "Edukasi orang tua tentang gizi seimbang dan pola makan anak.",

            'stunting' =>
                "Balita {$nama} (usia {$usia}) terdeteksi mengalami stunting " .
                "dengan nilai keyakinan " . round($cf * 100, 1) . "%. " .
                "Disarankan konsultasi ke tenaga kesehatan dan pemberian PMT. " .
                "Pantau pertumbuhan setiap bulan dan edukasi gizi orang tua.",

            'berisiko' =>
                "Balita {$nama} (usia {$usia}) berisiko mengalami stunting " .
                "dengan nilai keyakinan " . round($cf * 100, 1) . "%. " .
                "Lakukan pemantauan ketat setiap bulan. " .
                "Pastikan asupan gizi seimbang dan penuhi kebutuhan protein harian anak.",

            default =>
                "Balita {$nama} (usia {$usia}) dalam kondisi normal " .
                "dengan nilai keyakinan " . round($cf * 100, 1) . "%. " .
                "Pertahankan pola makan bergizi dan rutin kunjungi posyandu setiap bulan.",
        };
    }

    /**
     * Helper: bandingkan nilai dengan operator
     */
    private function bandingkan(
        float $nilai,
        string $operator,
        float $threshold
    ): bool {
        return match($operator) {
            '<'  => $nilai < $threshold,
            '<=' => $nilai <= $threshold,
            '>'  => $nilai > $threshold,
            '>=' => $nilai >= $threshold,
            '='  => abs($nilai - $threshold) < 0.001,
            default => false,
        };
    }

    /**
     * Hitung ulang CF untuk simulasi (tanpa simpan ke DB).
     * Dipakai di halaman diagnosis untuk preview hasil.
     */
    public function simulasi(
        Kunjungan $kunjungan,
        array $gejalaManu = []
    ): array {
        $kunjungan->load(['balita.riwayat', 'balita.kunjungan']);

        $gejalaTerdeteksi = $this->identifikasiGejala($kunjungan, $gejalaManu);
        $rules = RuleCf::aktif()->with('gejala')->get();
        $hasilCF = $this->hitungCFKombinasi($rules, $gejalaTerdeteksi);
        $status  = $this->tentukanStatus($hasilCF['cf_kombinasi']);

        // Format detail untuk ditampilkan di Vue
        $detail = $rules->map(function ($rule) use ($gejalaTerdeteksi, $hasilCF) {
            $aktif = $gejalaTerdeteksi[$rule->gejala_id] ?? false;
            return [
                'kode_rule'    => $rule->kode_rule,
                'nama_gejala'  => $rule->gejala->nama_gejala,
                'kategori'     => $rule->gejala->kategori,
                'sumber'       => $rule->gejala->sumber,
                'mb'           => $rule->mb,
                'md'           => $rule->md,
                'cf_pakar'     => $rule->getCfPakarAttribute(),
                'gejala_aktif' => $aktif,
                'cf_parsial'   => $aktif
                    ? ($hasilCF['cf_per_rule'][$rule->id] ?? 0)
                    : 0,
            ];
        });

        return [
            'cf_kombinasi'     => $hasilCF['cf_kombinasi'],
            'cf_persen'        => round($hasilCF['cf_kombinasi'] * 100, 1) . '%',
            'status_stunting'  => $status,
            'label_status'     => match($status) {
                'stunting_berat' => 'Stunting Berat',
                'stunting'       => 'Stunting',
                'berisiko'       => 'Berisiko',
                default          => 'Normal',
            },
            'rekomendasi'      => $this->generateRekomendasi(
                $status,
                $kunjungan->balita,
                $hasilCF['cf_kombinasi']
            ),
            'detail_gejala'    => $detail,
            'gejala_aktif'     => $detail->where('gejala_aktif', true)->count(),
            'total_gejala'     => $detail->count(),
        ];
    }
}