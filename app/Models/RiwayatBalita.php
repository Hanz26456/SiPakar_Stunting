<?php
// ===================================================
// app/Models/RiwayatBalita.php
// ===================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatBalita extends Model
{
    protected $table = 'riwayat_balita';

    protected $fillable = [
        'balita_id',
        'asi_eksklusif',
        'mulai_mpasi',
        'mpasi_sesuai_usia',
        'infeksi_berulang',
        'detail_penyakit',
        'berat_lahir',
        'panjang_lahir',
        'jenis_persalinan',
        'jamban_sehat',
        'air_bersih',
        'catatan',
    ];

    protected $casts = [
        'asi_eksklusif'    => 'boolean',
        'mulai_mpasi'      => 'date',
        'mpasi_sesuai_usia'=> 'boolean',
        'infeksi_berulang' => 'boolean',
        'jamban_sehat'     => 'boolean',
        'air_bersih'       => 'boolean',
    ];

    public function balita(): BelongsTo
    {
        return $this->belongsTo(Balita::class);
    }
}
