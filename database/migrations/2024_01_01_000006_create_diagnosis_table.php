<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel hasil diagnosis per kunjungan
        Schema::create('diagnosis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')
                  ->unique()
                  ->constrained('kunjungan')
                  ->cascadeOnDelete();

            // Kader yang menjalankan diagnosis
            $table->foreignId('kader_id')
                  ->constrained('users');

            // Hasil CF
            $table->float('cf_kombinasi')
                  ->comment('Nilai CF final hasil kombinasi semua rule');
            $table->enum('status_stunting', [
                'normal',
                'berisiko',
                'stunting',
                'stunting_berat'
            ]);

            // Rekomendasi otomatis dari sistem
            $table->text('rekomendasi')->nullable();

            // Verifikasi bidan
            $table->boolean('sudah_diverifikasi')->default(false);
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            // Override oleh bidan
            $table->enum('status_override', [
                'normal',
                'berisiko',
                'stunting',
                'stunting_berat'
            ])->nullable()->comment('Status yang dikoreksi bidan, null = ikut sistem');
            $table->text('alasan_override')->nullable();
            $table->text('catatan_bidan')->nullable();

            // Tindak lanjut
            $table->enum('tindak_lanjut', [
                'pantau',
                'edukasi_gizi',
                'pmt',
                'rujuk_puskesmas',
                'rujuk_rsud'
            ])->nullable();
            $table->date('jadwal_kontrol')->nullable();

            $table->timestamps();
        });

        // Tabel detail gejala yang aktif per diagnosis
        Schema::create('detail_diagnosis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnosis_id')
                  ->constrained('diagnosis')
                  ->cascadeOnDelete();
            $table->foreignId('rule_cf_id')
                  ->constrained('rule_cf');

            $table->boolean('gejala_aktif')
                  ->comment('Apakah gejala ini ditemukan pada balita');
            $table->float('cf_parsial')->nullable()
                  ->comment('Nilai CF rule ini setelah dikombinasikan');

            $table->timestamps();

            $table->unique(['diagnosis_id', 'rule_cf_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_diagnosis');
        Schema::dropIfExists('diagnosis');
    }
};
