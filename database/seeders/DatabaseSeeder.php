<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== USERS AWAL =====
        DB::table('users')->insert([
            [
                'name'       => 'Administrator',
                'email'      => 'admin@posyandu.id',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'dr. Sari Rahayu',
                'email'      => 'bidan@posyandu.id',
                'password'   => Hash::make('password'),
                'role'       => 'bidan',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Ratna Dewi',
                'email'      => 'kader@posyandu.id',
                'password'   => Hash::make('password'),
                'role'       => 'kader',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // ===== GEJALA =====
        $gejala = [
            [
                'kode'            => 'G-01',
                'nama_gejala'     => 'Tinggi badan/umur (TB/U) di bawah -2 SD standar WHO',
                'kategori'        => 'antropometri',
                'sumber'          => 'otomatis',
                'kolom_sumber'    => 'zscore_tbu',
                'operator'        => '<',
                'nilai_threshold' => -2.0,
                'deskripsi'       => 'Z-score TB/U dihitung otomatis dari data kunjungan',
            ],
            [
                'kode'            => 'G-02',
                'nama_gejala'     => 'Berat badan tidak naik selama 2 bulan berturut-turut',
                'kategori'        => 'pertumbuhan',
                'sumber'          => 'otomatis',
                'kolom_sumber'    => 'berat_badan',
                'operator'        => 'trend_flat',
                'nilai_threshold' => 2.0,
                'deskripsi'       => 'Sistem membandingkan BB 2 kunjungan terakhir secara otomatis',
            ],
            [
                'kode'            => 'G-03',
                'nama_gejala'     => 'Lingkar Lengan Atas (LILA) kurang dari 12.5 cm',
                'kategori'        => 'antropometri',
                'sumber'          => 'otomatis',
                'kolom_sumber'    => 'lila',
                'operator'        => '<',
                'nilai_threshold' => 12.5,
                'deskripsi'       => 'LILA diukur saat kunjungan dan dibandingkan otomatis',
            ],
            [
                'kode'            => 'G-04',
                'nama_gejala'     => 'ASI eksklusif tidak terpenuhi 0-6 bulan',
                'kategori'        => 'riwayat_gizi',
                'sumber'          => 'otomatis',
                'kolom_sumber'    => 'asi_eksklusif',
                'operator'        => '=',
                'nilai_threshold' => 0,
                'deskripsi'       => 'Diambil dari data riwayat balita',
            ],
            [
                'kode'            => 'G-05',
                'nama_gejala'     => 'Rambut tipis, kusam, dan mudah rontok',
                'kategori'        => 'klinis',
                'sumber'          => 'manual',
                'kolom_sumber'    => null,
                'operator'        => null,
                'nilai_threshold' => null,
                'deskripsi'       => 'Dinilai secara visual oleh kader saat kunjungan',
            ],
            [
                'kode'            => 'G-06',
                'nama_gejala'     => 'Anak tampak lesu, kurang aktif, dan mudah menangis',
                'kategori'        => 'klinis',
                'sumber'          => 'manual',
                'kolom_sumber'    => null,
                'operator'        => null,
                'nilai_threshold' => null,
                'deskripsi'       => 'Dinilai secara klinis oleh kader saat kunjungan',
            ],
            [
                'kode'            => 'G-07',
                'nama_gejala'     => 'Nafsu makan sangat menurun secara berkelanjutan',
                'kategori'        => 'klinis',
                'sumber'          => 'manual',
                'kolom_sumber'    => null,
                'operator'        => null,
                'nilai_threshold' => null,
                'deskripsi'       => 'Dilaporkan oleh orang tua saat kunjungan',
            ],
            [
                'kode'            => 'G-08',
                'nama_gejala'     => 'Berat badan/umur (BB/U) di bawah -2 SD standar WHO',
                'kategori'        => 'antropometri',
                'sumber'          => 'otomatis',
                'kolom_sumber'    => 'zscore_bbu',
                'operator'        => '<',
                'nilai_threshold' => -2.0,
                'deskripsi'       => 'Z-score BB/U dihitung otomatis dari data kunjungan',
            ],
            [
                'kode'            => 'G-09',
                'nama_gejala'     => 'Riwayat penyakit infeksi berulang (ISPA, diare, cacingan)',
                'kategori'        => 'riwayat_penyakit',
                'sumber'          => 'otomatis',
                'kolom_sumber'    => 'infeksi_berulang',
                'operator'        => '=',
                'nilai_threshold' => 1,
                'deskripsi'       => 'Diambil dari data riwayat balita',
            ],
            [
                'kode'            => 'G-10',
                'nama_gejala'     => 'MPASI tidak diberikan sesuai usia dan standar gizi',
                'kategori'        => 'riwayat_gizi',
                'sumber'          => 'otomatis',
                'kolom_sumber'    => 'mpasi_sesuai_usia',
                'operator'        => '=',
                'nilai_threshold' => 0,
                'deskripsi'       => 'Diambil dari data riwayat balita',
            ],
        ];

        foreach ($gejala as $g) {
            DB::table('gejala')->insert(array_merge($g, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // ===== RULE CF =====
        // Nilai MB & MD ini PLACEHOLDER — harus diisi ulang setelah wawancara pakar
        // CF pakar = MB - MD (dihitung otomatis via storedAs di migration)
        $rules = [
            // G-01: TB/U < -2 SD — gejala paling kuat
            ['kode' => 'R-01', 'gejala_kode' => 'G-01', 'status' => 'stunting',       'mb' => 0.8, 'md' => 0.1],
            // G-02: BB tidak naik 2 bulan
            ['kode' => 'R-02', 'gejala_kode' => 'G-02', 'status' => 'berisiko',       'mb' => 0.7, 'md' => 0.2],
            // G-03: LILA < 12.5 cm
            ['kode' => 'R-03', 'gejala_kode' => 'G-03', 'status' => 'berisiko',       'mb' => 0.6, 'md' => 0.2],
            // G-04: ASI tidak eksklusif
            ['kode' => 'R-04', 'gejala_kode' => 'G-04', 'status' => 'berisiko',       'mb' => 0.6, 'md' => 0.3],
            // G-05: Rambut tipis
            ['kode' => 'R-05', 'gejala_kode' => 'G-05', 'status' => 'berisiko',       'mb' => 0.5, 'md' => 0.3],
            // G-06: Lesu kurang aktif
            ['kode' => 'R-06', 'gejala_kode' => 'G-06', 'status' => 'berisiko',       'mb' => 0.5, 'md' => 0.3],
            // G-07: Nafsu makan turun
            ['kode' => 'R-07', 'gejala_kode' => 'G-07', 'status' => 'berisiko',       'mb' => 0.5, 'md' => 0.3],
            // G-08: BB/U < -2 SD
            ['kode' => 'R-08', 'gejala_kode' => 'G-08', 'status' => 'stunting',       'mb' => 0.7, 'md' => 0.1],
            // G-09: Infeksi berulang
            ['kode' => 'R-09', 'gejala_kode' => 'G-09', 'status' => 'berisiko',       'mb' => 0.6, 'md' => 0.2],
            // G-10: MPASI tidak sesuai
            ['kode' => 'R-10', 'gejala_kode' => 'G-10', 'status' => 'berisiko',       'mb' => 0.5, 'md' => 0.3],
            // Rule tambahan untuk stunting berat: TB/U < -3 SD (threshold -3)
            ['kode' => 'R-11', 'gejala_kode' => 'G-01', 'status' => 'stunting_berat', 'mb' => 0.9, 'md' => 0.05],
        ];

        foreach ($rules as $r) {
            $gejalaId = DB::table('gejala')
                          ->where('kode', $r['gejala_kode'])
                          ->value('id');

            DB::table('rule_cf')->insert([
                'gejala_id'      => $gejalaId,
                'kode_rule'      => $r['kode'],
                'status_diagnosa'=> $r['status'],
                'mb'             => $r['mb'],
                'md'             => $r['md'],
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
