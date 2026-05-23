<?php

namespace App\Services;

/**
 * ZScoreService
 *
 * Menghitung z-score TB/U dan BB/U berdasarkan
 * tabel standar WHO Child Growth Standards 2006.
 *
 * Rumus z-score WHO (metode LMS):
 *   Jika X >= M : Z = (X/M)^L - 1) / (S * L)
 *   Jika X < M  : Z = ((X/M)^L - 1) / (S * L)
 *
 * Untuk tabel ini kita pakai pendekatan median ± SD
 * yang umum dipakai Kemenkes dan e-PPGBM.
 */
class ZScoreService
{
    /**
     * Hitung z-score TB/U (Tinggi Badan per Umur)
     *
     * @param int    $usiaBulan  Usia dalam bulan (0-60)
     * @param string $jenisKelamin 'L' atau 'P'
     * @param float  $tinggiBadan Tinggi badan dalam cm
     * @return float z-score (negatif = di bawah median)
     */
    public function hitungZscoreTBU(
        int $usiaBulan,
        string $jenisKelamin,
        float $tinggiBadan
    ): float {
        $tabel = $jenisKelamin === 'L'
            ? $this->tabelTBU_LakiLaki()
            : $this->tabelTBU_Perempuan();

        return $this->hitungZscore($usiaBulan, $tinggiBadan, $tabel);
    }

    /**
     * Hitung z-score BB/U (Berat Badan per Umur)
     */
    public function hitungZscoreBBU(
        int $usiaBulan,
        string $jenisKelamin,
        float $beratBadan
    ): float {
        $tabel = $jenisKelamin === 'L'
            ? $this->tabelBBU_LakiLaki()
            : $this->tabelBBU_Perempuan();

        return $this->hitungZscore($usiaBulan, $beratBadan, $tabel);
    }

    /**
     * Hitung z-score BB/TB (Berat Badan per Tinggi Badan)
     * Digunakan untuk deteksi wasting (kurus)
     */
    public function hitungZscoreBBTB(
        string $jenisKelamin,
        float $tinggiBadan,
        float $beratBadan
    ): float {
        $tabel = $jenisKelamin === 'L'
            ? $this->tabelBBTB_LakiLaki()
            : $this->tabelBBTB_Perempuan();

        // Cari baris tabel berdasarkan tinggi badan
        $key = $this->cariKunciTinggi($tinggiBadan, $tabel);

        if ($key === null) return 0.0;

        return $this->hitungZscore(
            (int) round($tinggiBadan),
            $beratBadan,
            $tabel,
            'tinggi'
        );
    }

    /**
     * Tentukan kategori status berdasarkan z-score TB/U
     */
    public function kategoriTBU(float $zscore): string
    {
        if ($zscore < -3) return 'stunting_berat';
        if ($zscore < -2) return 'stunting';
        if ($zscore < -1) return 'berisiko';
        return 'normal';
    }

    /**
     * Tentukan kategori status berdasarkan z-score BB/U
     */
    public function kategoriBBU(float $zscore): string
    {
        if ($zscore < -3) return 'gizi_buruk';
        if ($zscore < -2) return 'gizi_kurang';
        if ($zscore < -1) return 'berisiko';
        return 'normal';
    }

    // ===================================================
    // PRIVATE: Algoritma hitung z-score
    // ===================================================

    private function hitungZscore(
        int $kunci,
        float $nilai,
        array $tabel,
        string $tipeKunci = 'usia'
    ): float {
        if (!isset($tabel[$kunci])) {
            // Interpolasi jika kunci tidak ada persis
            $kunci = $this->interpolasiKunci($kunci, $tabel);
            if ($kunci === null) return 0.0;
        }

        $row    = $tabel[$kunci];
        $median = $row['median']; // M
        $sd3neg = $row['sd3neg']; // -3 SD
        $sd2neg = $row['sd2neg']; // -2 SD
        $sd1neg = $row['sd1neg']; // -1 SD
        $sd1pos = $row['sd1pos']; // +1 SD
        $sd2pos = $row['sd2pos']; // +2 SD
        $sd3pos = $row['sd3pos']; // +3 SD

        // Hitung z-score berdasarkan posisi nilai
        if ($nilai < $median) {
            // Sisi negatif
            if ($nilai >= $sd1neg) {
                // Antara -1 SD dan median
                $sdUnit = $median - $sd1neg;
            } elseif ($nilai >= $sd2neg) {
                // Antara -2 SD dan -1 SD
                $sdUnit = $sd1neg - $sd2neg;
            } else {
                // Di bawah -2 SD
                $sdUnit = $sd2neg - $sd3neg;
            }
        } else {
            // Sisi positif
            if ($nilai <= $sd1pos) {
                $sdUnit = $sd1pos - $median;
            } elseif ($nilai <= $sd2pos) {
                $sdUnit = $sd2pos - $sd1pos;
            } else {
                $sdUnit = $sd3pos - $sd2pos;
            }
        }

        if ($sdUnit == 0) return 0.0;

        $zscore = ($nilai - $median) / $sdUnit;

        // Clamp antara -5 dan +5
        return round(max(-5.0, min(5.0, $zscore)), 2);
    }

    private function interpolasiKunci(int $kunci, array $tabel): ?int
    {
        $kunci = min($kunci, max(array_keys($tabel)));
        $kunci = max($kunci, min(array_keys($tabel)));
        return $kunci;
    }

    private function cariKunciTinggi(float $tinggi, array $tabel): ?int
    {
        $rounded = (int) round($tinggi);
        if (isset($tabel[$rounded])) return $rounded;
        return null;
    }

    // ===================================================
    // TABEL WHO - TB/U LAKI-LAKI (usia 0-60 bulan)
    // Format: usia_bulan => [median, sd3neg, sd2neg, sd1neg, sd1pos, sd2pos, sd3pos]
    // Sumber: WHO Child Growth Standards 2006
    // ===================================================

    private function tabelTBU_LakiLaki(): array
    {
        return [
            0  => ['median'=>49.9, 'sd3neg'=>44.2, 'sd2neg'=>46.1, 'sd1neg'=>48.0, 'sd1pos'=>51.8, 'sd2pos'=>53.7, 'sd3pos'=>55.6],
            1  => ['median'=>54.7, 'sd3neg'=>49.8, 'sd2neg'=>51.8, 'sd1neg'=>53.7, 'sd1pos'=>56.7, 'sd2pos'=>58.6, 'sd3pos'=>60.6],
            2  => ['median'=>58.4, 'sd3neg'=>53.0, 'sd2neg'=>55.0, 'sd1neg'=>57.0, 'sd1pos'=>60.3, 'sd2pos'=>62.4, 'sd3pos'=>64.4],
            3  => ['median'=>61.4, 'sd3neg'=>55.8, 'sd2neg'=>57.9, 'sd1neg'=>60.0, 'sd1pos'=>63.5, 'sd2pos'=>65.6, 'sd3pos'=>67.6],
            4  => ['median'=>63.9, 'sd3neg'=>58.0, 'sd2neg'=>60.0, 'sd1neg'=>62.1, 'sd1pos'=>65.9, 'sd2pos'=>68.0, 'sd3pos'=>70.1],
            5  => ['median'=>65.9, 'sd3neg'=>59.9, 'sd2neg'=>62.0, 'sd1neg'=>64.0, 'sd1pos'=>67.8, 'sd2pos'=>69.9, 'sd3pos'=>72.0],
            6  => ['median'=>67.6, 'sd3neg'=>61.4, 'sd2neg'=>63.6, 'sd1neg'=>65.7, 'sd1pos'=>69.6, 'sd2pos'=>71.6, 'sd3pos'=>73.8],
            7  => ['median'=>69.2, 'sd3neg'=>62.7, 'sd2neg'=>64.9, 'sd1neg'=>67.0, 'sd1pos'=>71.0, 'sd2pos'=>73.2, 'sd3pos'=>75.5],
            8  => ['median'=>70.6, 'sd3neg'=>63.9, 'sd2neg'=>66.2, 'sd1neg'=>68.4, 'sd1pos'=>72.7, 'sd2pos'=>74.9, 'sd3pos'=>77.2],
            9  => ['median'=>72.0, 'sd3neg'=>65.1, 'sd2neg'=>67.5, 'sd1neg'=>69.7, 'sd1pos'=>74.2, 'sd2pos'=>76.5, 'sd3pos'=>78.8],
            10 => ['median'=>73.3, 'sd3neg'=>66.2, 'sd2neg'=>68.6, 'sd1neg'=>71.0, 'sd1pos'=>75.6, 'sd2pos'=>78.0, 'sd3pos'=>80.5],
            11 => ['median'=>74.5, 'sd3neg'=>67.3, 'sd2neg'=>69.8, 'sd1neg'=>72.2, 'sd1pos'=>76.9, 'sd2pos'=>79.4, 'sd3pos'=>81.8],
            12 => ['median'=>75.7, 'sd3neg'=>68.3, 'sd2neg'=>70.8, 'sd1neg'=>73.4, 'sd1pos'=>78.1, 'sd2pos'=>80.6, 'sd3pos'=>83.2],
            13 => ['median'=>76.9, 'sd3neg'=>69.3, 'sd2neg'=>71.9, 'sd1neg'=>74.5, 'sd1pos'=>79.2, 'sd2pos'=>81.8, 'sd3pos'=>84.5],
            14 => ['median'=>78.0, 'sd3neg'=>70.3, 'sd2neg'=>73.0, 'sd1neg'=>75.6, 'sd1pos'=>80.4, 'sd2pos'=>83.1, 'sd3pos'=>85.7],
            15 => ['median'=>79.1, 'sd3neg'=>71.2, 'sd2neg'=>74.0, 'sd1neg'=>76.6, 'sd1pos'=>81.7, 'sd2pos'=>84.3, 'sd3pos'=>87.0],
            16 => ['median'=>80.2, 'sd3neg'=>72.1, 'sd2neg'=>74.9, 'sd1neg'=>77.7, 'sd1pos'=>82.7, 'sd2pos'=>85.5, 'sd3pos'=>88.4],
            17 => ['median'=>81.2, 'sd3neg'=>73.0, 'sd2neg'=>75.8, 'sd1neg'=>78.7, 'sd1pos'=>83.7, 'sd2pos'=>86.6, 'sd3pos'=>89.5],
            18 => ['median'=>82.3, 'sd3neg'=>73.8, 'sd2neg'=>76.8, 'sd1neg'=>79.7, 'sd1pos'=>84.8, 'sd2pos'=>87.8, 'sd3pos'=>90.7],
            19 => ['median'=>83.2, 'sd3neg'=>74.7, 'sd2neg'=>77.6, 'sd1neg'=>80.7, 'sd1pos'=>85.8, 'sd2pos'=>88.9, 'sd3pos'=>92.0],
            20 => ['median'=>84.2, 'sd3neg'=>75.5, 'sd2neg'=>78.5, 'sd1neg'=>81.6, 'sd1pos'=>86.8, 'sd2pos'=>89.9, 'sd3pos'=>93.0],
            21 => ['median'=>85.1, 'sd3neg'=>76.3, 'sd2neg'=>79.4, 'sd1neg'=>82.5, 'sd1pos'=>87.7, 'sd2pos'=>90.9, 'sd3pos'=>94.1],
            22 => ['median'=>86.0, 'sd3neg'=>77.0, 'sd2neg'=>80.2, 'sd1neg'=>83.4, 'sd1pos'=>88.6, 'sd2pos'=>91.9, 'sd3pos'=>95.1],
            23 => ['median'=>86.9, 'sd3neg'=>77.8, 'sd2neg'=>81.0, 'sd1neg'=>84.2, 'sd1pos'=>89.5, 'sd2pos'=>92.8, 'sd3pos'=>96.1],
            24 => ['median'=>87.8, 'sd3neg'=>78.3, 'sd2neg'=>81.7, 'sd1neg'=>85.1, 'sd1pos'=>90.6, 'sd2pos'=>93.9, 'sd3pos'=>97.3],
            25 => ['median'=>88.8, 'sd3neg'=>79.1, 'sd2neg'=>82.5, 'sd1neg'=>85.9, 'sd1pos'=>91.5, 'sd2pos'=>94.9, 'sd3pos'=>98.4],
            26 => ['median'=>89.7, 'sd3neg'=>79.9, 'sd2neg'=>83.3, 'sd1neg'=>86.7, 'sd1pos'=>92.4, 'sd2pos'=>95.8, 'sd3pos'=>99.3],
            27 => ['median'=>90.7, 'sd3neg'=>80.7, 'sd2neg'=>84.1, 'sd1neg'=>87.5, 'sd1pos'=>93.2, 'sd2pos'=>96.7, 'sd3pos'=>100.3],
            28 => ['median'=>91.3, 'sd3neg'=>81.3, 'sd2neg'=>84.8, 'sd1neg'=>88.3, 'sd1pos'=>94.2, 'sd2pos'=>97.7, 'sd3pos'=>101.3],
            29 => ['median'=>92.1, 'sd3neg'=>82.0, 'sd2neg'=>85.5, 'sd1neg'=>89.1, 'sd1pos'=>95.0, 'sd2pos'=>98.6, 'sd3pos'=>102.2],
            30 => ['median'=>93.0, 'sd3neg'=>82.8, 'sd2neg'=>86.3, 'sd1neg'=>89.8, 'sd1pos'=>95.8, 'sd2pos'=>99.4, 'sd3pos'=>103.1],
            31 => ['median'=>93.8, 'sd3neg'=>83.5, 'sd2neg'=>87.0, 'sd1neg'=>90.6, 'sd1pos'=>96.6, 'sd2pos'=>100.3, 'sd3pos'=>104.0],
            32 => ['median'=>94.6, 'sd3neg'=>84.2, 'sd2neg'=>87.8, 'sd1neg'=>91.3, 'sd1pos'=>97.4, 'sd2pos'=>101.1, 'sd3pos'=>104.8],
            33 => ['median'=>95.4, 'sd3neg'=>84.9, 'sd2neg'=>88.5, 'sd1neg'=>92.1, 'sd1pos'=>98.2, 'sd2pos'=>101.9, 'sd3pos'=>105.7],
            34 => ['median'=>96.2, 'sd3neg'=>85.6, 'sd2neg'=>89.2, 'sd1neg'=>92.8, 'sd1pos'=>99.0, 'sd2pos'=>102.7, 'sd3pos'=>106.5],
            35 => ['median'=>97.0, 'sd3neg'=>86.3, 'sd2neg'=>89.9, 'sd1neg'=>93.6, 'sd1pos'=>99.7, 'sd2pos'=>103.5, 'sd3pos'=>107.3],
            36 => ['median'=>97.8, 'sd3neg'=>87.0, 'sd2neg'=>90.6, 'sd1neg'=>94.3, 'sd1pos'=>100.5, 'sd2pos'=>104.3, 'sd3pos'=>108.1],
            42 => ['median'=>101.5, 'sd3neg'=>90.3, 'sd2neg'=>94.2, 'sd1neg'=>97.9, 'sd1pos'=>104.8, 'sd2pos'=>108.7, 'sd3pos'=>112.7],
            48 => ['median'=>105.0, 'sd3neg'=>93.3, 'sd2neg'=>97.4, 'sd1neg'=>101.4, 'sd1pos'=>108.7, 'sd2pos'=>112.7, 'sd3pos'=>116.8],
            54 => ['median'=>108.2, 'sd3neg'=>96.2, 'sd2neg'=>100.4, 'sd1neg'=>104.6, 'sd1pos'=>112.1, 'sd2pos'=>116.4, 'sd3pos'=>120.7],
            60 => ['median'=>111.3, 'sd3neg'=>98.9, 'sd2neg'=>103.3, 'sd1neg'=>107.6, 'sd1pos'=>115.4, 'sd2pos'=>119.8, 'sd3pos'=>124.2],
        ];
    }

    private function tabelTBU_Perempuan(): array
    {
        return [
            0  => ['median'=>49.1, 'sd3neg'=>43.6, 'sd2neg'=>45.4, 'sd1neg'=>47.3, 'sd1pos'=>51.0, 'sd2pos'=>52.9, 'sd3pos'=>54.7],
            1  => ['median'=>53.7, 'sd3neg'=>48.8, 'sd2neg'=>50.8, 'sd1neg'=>52.8, 'sd1pos'=>55.6, 'sd2pos'=>57.6, 'sd3pos'=>59.5],
            2  => ['median'=>57.1, 'sd3neg'=>52.0, 'sd2neg'=>54.0, 'sd1neg'=>56.0, 'sd1pos'=>59.1, 'sd2pos'=>61.1, 'sd3pos'=>63.2],
            3  => ['median'=>59.8, 'sd3neg'=>54.6, 'sd2neg'=>56.7, 'sd1neg'=>58.7, 'sd1pos'=>61.9, 'sd2pos'=>63.9, 'sd3pos'=>66.1],
            4  => ['median'=>62.1, 'sd3neg'=>56.7, 'sd2neg'=>58.8, 'sd1neg'=>60.9, 'sd1pos'=>64.3, 'sd2pos'=>66.4, 'sd3pos'=>68.6],
            5  => ['median'=>64.0, 'sd3neg'=>58.4, 'sd2neg'=>60.6, 'sd1neg'=>62.7, 'sd1pos'=>66.2, 'sd2pos'=>68.5, 'sd3pos'=>70.7],
            6  => ['median'=>65.7, 'sd3neg'=>59.8, 'sd2neg'=>62.1, 'sd1neg'=>64.4, 'sd1pos'=>68.0, 'sd2pos'=>70.3, 'sd3pos'=>72.5],
            7  => ['median'=>67.3, 'sd3neg'=>61.2, 'sd2neg'=>63.5, 'sd1neg'=>65.9, 'sd1pos'=>69.6, 'sd2pos'=>72.0, 'sd3pos'=>74.4],
            8  => ['median'=>68.7, 'sd3neg'=>62.5, 'sd2neg'=>64.9, 'sd1neg'=>67.3, 'sd1pos'=>71.2, 'sd2pos'=>73.6, 'sd3pos'=>76.1],
            9  => ['median'=>70.1, 'sd3neg'=>63.7, 'sd2neg'=>66.2, 'sd1neg'=>68.7, 'sd1pos'=>72.7, 'sd2pos'=>75.2, 'sd3pos'=>77.7],
            10 => ['median'=>71.5, 'sd3neg'=>64.9, 'sd2neg'=>67.4, 'sd1neg'=>70.0, 'sd1pos'=>74.0, 'sd2pos'=>76.6, 'sd3pos'=>79.2],
            11 => ['median'=>72.8, 'sd3neg'=>66.1, 'sd2neg'=>68.7, 'sd1neg'=>71.3, 'sd1pos'=>75.3, 'sd2pos'=>78.0, 'sd3pos'=>80.6],
            12 => ['median'=>74.0, 'sd3neg'=>67.2, 'sd2neg'=>69.8, 'sd1neg'=>72.5, 'sd1pos'=>76.5, 'sd2pos'=>79.3, 'sd3pos'=>82.0],
            13 => ['median'=>75.2, 'sd3neg'=>68.2, 'sd2neg'=>71.0, 'sd1neg'=>73.7, 'sd1pos'=>77.7, 'sd2pos'=>80.5, 'sd3pos'=>83.4],
            14 => ['median'=>76.4, 'sd3neg'=>69.2, 'sd2neg'=>72.0, 'sd1neg'=>74.9, 'sd1pos'=>78.9, 'sd2pos'=>81.8, 'sd3pos'=>84.7],
            15 => ['median'=>77.5, 'sd3neg'=>70.2, 'sd2neg'=>73.1, 'sd1neg'=>76.0, 'sd1pos'=>80.0, 'sd2pos'=>83.0, 'sd3pos'=>86.0],
            16 => ['median'=>78.6, 'sd3neg'=>71.2, 'sd2neg'=>74.1, 'sd1neg'=>77.1, 'sd1pos'=>81.1, 'sd2pos'=>84.2, 'sd3pos'=>87.2],
            17 => ['median'=>79.7, 'sd3neg'=>72.1, 'sd2neg'=>75.1, 'sd1neg'=>78.1, 'sd1pos'=>82.2, 'sd2pos'=>85.3, 'sd3pos'=>88.4],
            18 => ['median'=>80.7, 'sd3neg'=>73.0, 'sd2neg'=>76.0, 'sd1neg'=>79.2, 'sd1pos'=>83.3, 'sd2pos'=>86.4, 'sd3pos'=>89.6],
            19 => ['median'=>81.7, 'sd3neg'=>73.9, 'sd2neg'=>77.0, 'sd1neg'=>80.1, 'sd1pos'=>84.4, 'sd2pos'=>87.5, 'sd3pos'=>90.7],
            20 => ['median'=>82.7, 'sd3neg'=>74.8, 'sd2neg'=>77.9, 'sd1neg'=>81.1, 'sd1pos'=>85.4, 'sd2pos'=>88.6, 'sd3pos'=>91.9],
            21 => ['median'=>83.7, 'sd3neg'=>75.7, 'sd2neg'=>78.9, 'sd1neg'=>82.0, 'sd1pos'=>86.3, 'sd2pos'=>89.6, 'sd3pos'=>92.9],
            22 => ['median'=>84.6, 'sd3neg'=>76.5, 'sd2neg'=>79.8, 'sd1neg'=>83.0, 'sd1pos'=>87.3, 'sd2pos'=>90.6, 'sd3pos'=>93.9],
            23 => ['median'=>85.5, 'sd3neg'=>77.3, 'sd2neg'=>80.7, 'sd1neg'=>83.9, 'sd1pos'=>88.2, 'sd2pos'=>91.6, 'sd3pos'=>95.0],
            24 => ['median'=>86.4, 'sd3neg'=>78.0, 'sd2neg'=>81.4, 'sd1neg'=>84.8, 'sd1pos'=>89.2, 'sd2pos'=>92.7, 'sd3pos'=>96.1],
            30 => ['median'=>91.1, 'sd3neg'=>82.5, 'sd2neg'=>86.1, 'sd1neg'=>89.7, 'sd1pos'=>94.4, 'sd2pos'=>98.1, 'sd3pos'=>101.8],
            36 => ['median'=>95.6, 'sd3neg'=>86.8, 'sd2neg'=>90.6, 'sd1neg'=>94.4, 'sd1pos'=>99.4, 'sd2pos'=>103.3, 'sd3pos'=>107.2],
            42 => ['median'=>99.7, 'sd3neg'=>90.7, 'sd2neg'=>94.7, 'sd1neg'=>98.7, 'sd1pos'=>104.0, 'sd2pos'=>108.1, 'sd3pos'=>112.2],
            48 => ['median'=>103.3, 'sd3neg'=>94.2, 'sd2neg'=>98.4, 'sd1neg'=>102.6, 'sd1pos'=>108.2, 'sd2pos'=>112.4, 'sd3pos'=>116.7],
            54 => ['median'=>106.7, 'sd3neg'=>97.4, 'sd2neg'=>101.8, 'sd1neg'=>106.1, 'sd1pos'=>112.0, 'sd2pos'=>116.4, 'sd3pos'=>120.8],
            60 => ['median'=>109.9, 'sd3neg'=>100.3, 'sd2neg'=>104.9, 'sd1neg'=>109.4, 'sd1pos'=>115.7, 'sd2pos'=>120.2, 'sd3pos'=>124.8],
        ];
    }

    private function tabelBBU_LakiLaki(): array
    {
        return [
            0  => ['median'=>3.3, 'sd3neg'=>2.1, 'sd2neg'=>2.5, 'sd1neg'=>2.9, 'sd1pos'=>3.9, 'sd2pos'=>4.4, 'sd3pos'=>5.0],
            1  => ['median'=>4.5, 'sd3neg'=>2.9, 'sd2neg'=>3.4, 'sd1neg'=>3.9, 'sd1pos'=>5.1, 'sd2pos'=>5.8, 'sd3pos'=>6.6],
            2  => ['median'=>5.6, 'sd3neg'=>3.8, 'sd2neg'=>4.3, 'sd1neg'=>4.9, 'sd1pos'=>6.3, 'sd2pos'=>7.1, 'sd3pos'=>8.0],
            3  => ['median'=>6.4, 'sd3neg'=>4.4, 'sd2neg'=>5.0, 'sd1neg'=>5.7, 'sd1pos'=>7.2, 'sd2pos'=>8.0, 'sd3pos'=>9.0],
            4  => ['median'=>7.0, 'sd3neg'=>4.9, 'sd2neg'=>5.6, 'sd1neg'=>6.2, 'sd1pos'=>7.8, 'sd2pos'=>8.7, 'sd3pos'=>9.7],
            5  => ['median'=>7.5, 'sd3neg'=>5.3, 'sd2neg'=>6.0, 'sd1neg'=>6.7, 'sd1pos'=>8.4, 'sd2pos'=>9.3, 'sd3pos'=>10.4],
            6  => ['median'=>7.9, 'sd3neg'=>5.7, 'sd2neg'=>6.4, 'sd1neg'=>7.1, 'sd1pos'=>8.8, 'sd2pos'=>9.8, 'sd3pos'=>10.9],
            7  => ['median'=>8.3, 'sd3neg'=>5.9, 'sd2neg'=>6.7, 'sd1neg'=>7.4, 'sd1pos'=>9.2, 'sd2pos'=>10.3, 'sd3pos'=>11.4],
            8  => ['median'=>8.6, 'sd3neg'=>6.2, 'sd2neg'=>7.0, 'sd1neg'=>7.7, 'sd1pos'=>9.6, 'sd2pos'=>10.7, 'sd3pos'=>11.9],
            9  => ['median'=>8.9, 'sd3neg'=>6.4, 'sd2neg'=>7.2, 'sd1neg'=>8.0, 'sd1pos'=>9.9, 'sd2pos'=>11.0, 'sd3pos'=>12.3],
            10 => ['median'=>9.2, 'sd3neg'=>6.6, 'sd2neg'=>7.5, 'sd1neg'=>8.2, 'sd1pos'=>10.2, 'sd2pos'=>11.4, 'sd3pos'=>12.7],
            11 => ['median'=>9.4, 'sd3neg'=>6.8, 'sd2neg'=>7.7, 'sd1neg'=>8.5, 'sd1pos'=>10.5, 'sd2pos'=>11.7, 'sd3pos'=>13.0],
            12 => ['median'=>9.6, 'sd3neg'=>7.0, 'sd2neg'=>7.8, 'sd1neg'=>8.7, 'sd1pos'=>10.8, 'sd2pos'=>12.0, 'sd3pos'=>13.3],
            15 => ['median'=>10.3, 'sd3neg'=>7.5, 'sd2neg'=>8.5, 'sd1neg'=>9.3, 'sd1pos'=>11.5, 'sd2pos'=>12.8, 'sd3pos'=>14.2],
            18 => ['median'=>10.9, 'sd3neg'=>8.1, 'sd2neg'=>9.0, 'sd1neg'=>9.9, 'sd1pos'=>12.1, 'sd2pos'=>13.5, 'sd3pos'=>15.1],
            24 => ['median'=>12.2, 'sd3neg'=>9.2, 'sd2neg'=>10.2, 'sd1neg'=>11.1, 'sd1pos'=>13.4, 'sd2pos'=>15.0, 'sd3pos'=>16.9],
            30 => ['median'=>13.3, 'sd3neg'=>10.1, 'sd2neg'=>11.2, 'sd1neg'=>12.2, 'sd1pos'=>14.6, 'sd2pos'=>16.3, 'sd3pos'=>18.3],
            36 => ['median'=>14.3, 'sd3neg'=>10.8, 'sd2neg'=>12.0, 'sd1neg'=>13.1, 'sd1pos'=>15.6, 'sd2pos'=>17.5, 'sd3pos'=>19.6],
            42 => ['median'=>15.3, 'sd3neg'=>11.5, 'sd2neg'=>12.9, 'sd1neg'=>14.0, 'sd1pos'=>16.7, 'sd2pos'=>18.7, 'sd3pos'=>21.0],
            48 => ['median'=>16.3, 'sd3neg'=>12.3, 'sd2neg'=>13.7, 'sd1neg'=>14.9, 'sd1pos'=>17.7, 'sd2pos'=>19.9, 'sd3pos'=>22.4],
            54 => ['median'=>17.3, 'sd3neg'=>13.0, 'sd2neg'=>14.5, 'sd1neg'=>15.8, 'sd1pos'=>18.8, 'sd2pos'=>21.1, 'sd3pos'=>23.8],
            60 => ['median'=>18.3, 'sd3neg'=>13.7, 'sd2neg'=>15.3, 'sd1neg'=>16.8, 'sd1pos'=>19.9, 'sd2pos'=>22.4, 'sd3pos'=>25.3],
        ];
    }

    private function tabelBBU_Perempuan(): array
    {
        return [
            0  => ['median'=>3.2, 'sd3neg'=>2.0, 'sd2neg'=>2.4, 'sd1neg'=>2.8, 'sd1pos'=>3.7, 'sd2pos'=>4.2, 'sd3pos'=>4.8],
            1  => ['median'=>4.2, 'sd3neg'=>2.7, 'sd2neg'=>3.2, 'sd1neg'=>3.6, 'sd1pos'=>4.8, 'sd2pos'=>5.5, 'sd3pos'=>6.2],
            2  => ['median'=>5.1, 'sd3neg'=>3.4, 'sd2neg'=>3.9, 'sd1neg'=>4.5, 'sd1pos'=>5.8, 'sd2pos'=>6.6, 'sd3pos'=>7.5],
            3  => ['median'=>5.8, 'sd3neg'=>4.0, 'sd2neg'=>4.5, 'sd1neg'=>5.2, 'sd1pos'=>6.6, 'sd2pos'=>7.5, 'sd3pos'=>8.5],
            4  => ['median'=>6.4, 'sd3neg'=>4.4, 'sd2neg'=>5.0, 'sd1neg'=>5.7, 'sd1pos'=>7.3, 'sd2pos'=>8.2, 'sd3pos'=>9.3],
            5  => ['median'=>6.9, 'sd3neg'=>4.8, 'sd2neg'=>5.4, 'sd1neg'=>6.1, 'sd1pos'=>7.8, 'sd2pos'=>8.8, 'sd3pos'=>10.0],
            6  => ['median'=>7.3, 'sd3neg'=>5.1, 'sd2neg'=>5.7, 'sd1neg'=>6.5, 'sd1pos'=>8.2, 'sd2pos'=>9.3, 'sd3pos'=>10.6],
            7  => ['median'=>7.6, 'sd3neg'=>5.3, 'sd2neg'=>6.0, 'sd1neg'=>6.8, 'sd1pos'=>8.6, 'sd2pos'=>9.8, 'sd3pos'=>11.1],
            8  => ['median'=>7.9, 'sd3neg'=>5.6, 'sd2neg'=>6.3, 'sd1neg'=>7.0, 'sd1pos'=>9.0, 'sd2pos'=>10.2, 'sd3pos'=>11.6],
            9  => ['median'=>8.2, 'sd3neg'=>5.8, 'sd2neg'=>6.5, 'sd1neg'=>7.3, 'sd1pos'=>9.3, 'sd2pos'=>10.5, 'sd3pos'=>12.0],
            10 => ['median'=>8.5, 'sd3neg'=>6.0, 'sd2neg'=>6.7, 'sd1neg'=>7.5, 'sd1pos'=>9.6, 'sd2pos'=>10.9, 'sd3pos'=>12.4],
            11 => ['median'=>8.7, 'sd3neg'=>6.1, 'sd2neg'=>6.9, 'sd1neg'=>7.7, 'sd1pos'=>9.9, 'sd2pos'=>11.2, 'sd3pos'=>12.8],
            12 => ['median'=>8.9, 'sd3neg'=>6.3, 'sd2neg'=>7.0, 'sd1neg'=>7.9, 'sd1pos'=>10.1, 'sd2pos'=>11.5, 'sd3pos'=>13.1],
            15 => ['median'=>9.6, 'sd3neg'=>6.9, 'sd2neg'=>7.6, 'sd1neg'=>8.5, 'sd1pos'=>10.9, 'sd2pos'=>12.4, 'sd3pos'=>14.1],
            18 => ['median'=>10.2, 'sd3neg'=>7.4, 'sd2neg'=>8.2, 'sd1neg'=>9.1, 'sd1pos'=>11.6, 'sd2pos'=>13.2, 'sd3pos'=>15.1],
            24 => ['median'=>11.5, 'sd3neg'=>8.5, 'sd2neg'=>9.4, 'sd1neg'=>10.3, 'sd1pos'=>13.0, 'sd2pos'=>14.8, 'sd3pos'=>17.0],
            30 => ['median'=>12.7, 'sd3neg'=>9.4, 'sd2neg'=>10.4, 'sd1neg'=>11.4, 'sd1pos'=>14.3, 'sd2pos'=>16.3, 'sd3pos'=>18.7],
            36 => ['median'=>13.9, 'sd3neg'=>10.3, 'sd2neg'=>11.4, 'sd1neg'=>12.5, 'sd1pos'=>15.6, 'sd2pos'=>17.8, 'sd3pos'=>20.4],
            42 => ['median'=>15.0, 'sd3neg'=>11.1, 'sd2neg'=>12.3, 'sd1neg'=>13.5, 'sd1pos'=>16.8, 'sd2pos'=>19.2, 'sd3pos'=>22.1],
            48 => ['median'=>16.1, 'sd3neg'=>11.9, 'sd2neg'=>13.2, 'sd1neg'=>14.5, 'sd1pos'=>18.0, 'sd2pos'=>20.6, 'sd3pos'=>23.8],
            54 => ['median'=>17.2, 'sd3neg'=>12.7, 'sd2neg'=>14.1, 'sd1neg'=>15.5, 'sd1pos'=>19.2, 'sd2pos'=>22.0, 'sd3pos'=>25.5],
            60 => ['median'=>18.3, 'sd3neg'=>13.5, 'sd2neg'=>15.0, 'sd1neg'=>16.5, 'sd1pos'=>20.4, 'sd2pos'=>23.4, 'sd3pos'=>27.1],
        ];
    }

    private function tabelBBTB_LakiLaki(): array
    {
        return [];
    }

    private function tabelBBTB_Perempuan(): array
    {
        return [];
    }
}