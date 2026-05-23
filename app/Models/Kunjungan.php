<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $fillable = [
        'balita_id',
        'kader_id',
        'tanggal_kunjungan',
        'berat_badan',
        'tinggi_badan',
        'lila',
        'lingkar_kepala',
        'zscore_tbu',
        'zscore_bbu',
        'zscore_bbtb',
        'usia_bulan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'berat_badan'       => 'float',
        'tinggi_badan'      => 'float',
        'lila'              => 'float',
        'lingkar_kepala'    => 'float',
        'zscore_tbu'        => 'float',
        'zscore_bbu'        => 'float',
        'zscore_bbtb'       => 'float',
        'usia_bulan'        => 'integer',
    ];

    // ===== ACCESSORS =====

    // Label z-score TB/U
    public function getStatusZscoreTbuAttribute(): string
    {
        $z = $this->zscore_tbu;
        if ($z === null) return 'Belum diukur';
        if ($z < -3)  return 'Stunting berat';
        if ($z < -2)  return 'Stunting';
        if ($z < -1)  return 'Berisiko';
        return 'Normal';
    }

    // Warna status untuk UI
    public function getWarnaStatusAttribute(): string
    {
        return match($this->status_zscore_tbu) {
            'Stunting berat' => 'red',
            'Stunting'       => 'red',
            'Berisiko'       => 'amber',
            default          => 'green',
        };
    }

    // ===== RELATIONSHIPS =====

    public function balita(): BelongsTo
    {
        return $this->belongsTo(Balita::class);
    }

    public function kader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kader_id');
    }

    public function diagnosis(): HasOne
    {
        return $this->hasOne(Diagnosis::class, 'kunjungan_id');
    }

    // ===== SCOPES =====

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_kunjungan', now()->month)
                     ->whereYear('tanggal_kunjungan', now()->year);
    }

    public function scopeTerbaru($query)
    {
        return $query->orderBy('tanggal_kunjungan', 'desc');
    }
}
