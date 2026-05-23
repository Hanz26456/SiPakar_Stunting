<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_balita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')
                  ->constrained('balita')
                  ->cascadeOnDelete();

            // Riwayat ASI & MPASI
            $table->boolean('asi_eksklusif')->default(false)
                  ->comment('ASI eksklusif 0-6 bulan terpenuhi');
            $table->date('mulai_mpasi')->nullable()
                  ->comment('Tanggal mulai MPASI');
            $table->boolean('mpasi_sesuai_usia')->default(false)
                  ->comment('MPASI diberikan sesuai usia dan standar gizi');

            // Riwayat penyakit
            $table->boolean('infeksi_berulang')->default(false)
                  ->comment('Riwayat ISPA/diare/cacingan berulang');
            $table->text('detail_penyakit')->nullable()
                  ->comment('Catatan detail riwayat penyakit');

            // Riwayat kelahiran
            $table->float('berat_lahir')->nullable()
                  ->comment('Berat badan saat lahir dalam kg');
            $table->float('panjang_lahir')->nullable()
                  ->comment('Panjang badan saat lahir dalam cm');
            $table->enum('jenis_persalinan', ['normal', 'caesar', 'lainnya'])->nullable();

            // Faktor sosial ekonomi
            $table->boolean('jamban_sehat')->nullable();
            $table->boolean('air_bersih')->nullable();

            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_balita');
    }
};
