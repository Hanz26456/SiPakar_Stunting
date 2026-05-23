<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')
                  ->constrained('balita')
                  ->cascadeOnDelete();

            // Kader yang menginput
            $table->foreignId('kader_id')
                  ->constrained('users');

            $table->date('tanggal_kunjungan');

            // Data antropometri
            $table->float('berat_badan')
                  ->comment('Berat badan dalam kg');
            $table->float('tinggi_badan')
                  ->comment('Tinggi/panjang badan dalam cm');
            $table->float('lila')->nullable()
                  ->comment('Lingkar Lengan Atas dalam cm');
            $table->float('lingkar_kepala')->nullable()
                  ->comment('Lingkar kepala dalam cm');

            // Z-score (dihitung otomatis oleh sistem)
            $table->float('zscore_tbu')->nullable()
                  ->comment('Z-score Tinggi Badan per Umur');
            $table->float('zscore_bbu')->nullable()
                  ->comment('Z-score Berat Badan per Umur');
            $table->float('zscore_bbtb')->nullable()
                  ->comment('Z-score Berat Badan per Tinggi Badan');

            // Usia saat kunjungan (disimpan agar tidak perlu hitung ulang)
            $table->integer('usia_bulan')
                  ->comment('Usia balita dalam bulan saat kunjungan');

            $table->text('catatan')->nullable();
            $table->timestamps();

            // Satu balita hanya boleh satu kunjungan per bulan
            $table->unique(['balita_id', 'tanggal_kunjungan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
