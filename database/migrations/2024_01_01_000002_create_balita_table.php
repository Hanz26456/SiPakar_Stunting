<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balita', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('nama_ibu');
            $table->string('nama_ayah')->nullable();
            $table->string('no_hp_ortu', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt_rw', 10)->nullable();
            $table->string('desa')->nullable();
            $table->string('no_kk', 20)->nullable();

            // Link ke akun orang tua (opsional)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Kader yang mendaftarkan
            $table->foreignId('kader_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balita');
    }
};
