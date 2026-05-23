<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel master gejala
        Schema::create('gejala', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique()
                  ->comment('Kode gejala, contoh: G-01');
            $table->string('nama_gejala');
            $table->enum('kategori', [
                'antropometri',
                'pertumbuhan',
                'klinis',
                'riwayat_gizi',
                'riwayat_penyakit'
            ]);
            $table->text('deskripsi')->nullable();

            // Sumber data gejala: dari input manual kader atau otomatis dari data kunjungan
            $table->enum('sumber', ['manual', 'otomatis'])->default('manual')
                  ->comment('manual = kader centang, otomatis = sistem deteksi dari data');

            // Untuk gejala otomatis, definisikan kondisinya
            $table->string('kolom_sumber')->nullable()
                  ->comment('Kolom kunjungan yang jadi sumber, contoh: zscore_tbu');
            $table->string('operator')->nullable()
                  ->comment('Operator perbandingan, contoh: <, >, <=, >=');
            $table->float('nilai_threshold')->nullable()
                  ->comment('Nilai ambang batas, contoh: -2 untuk zscore');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel rule CF dari pakar
        Schema::create('rule_cf', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gejala_id')
                  ->constrained('gejala')
                  ->cascadeOnDelete();

            $table->string('kode_rule', 10)->unique()
                  ->comment('Kode rule, contoh: R-01');

            // Kondisi tambahan rule (opsional, untuk rule kompleks)
            $table->text('kondisi')->nullable()
                  ->comment('Deskripsi kondisi rule ini berlaku');

            // Status diagnosis yang disasar rule ini
            $table->enum('status_diagnosa', [
                'normal',
                'berisiko',
                'stunting',
                'stunting_berat'
            ]);

            // Nilai CF dari pakar (hasil wawancara)
            $table->float('mb')->comment('Measure of Belief (0.0 - 1.0)');
            $table->float('md')->comment('Measure of Disbelief (0.0 - 1.0)');
            $table->float('cf_pakar')
                  ->storedAs('mb - md')
                  ->comment('CF = MB - MD, dihitung otomatis');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_cf');
        Schema::dropIfExists('gejala');
    }
};
