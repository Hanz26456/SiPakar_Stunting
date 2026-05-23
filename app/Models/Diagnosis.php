<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diagnosis extends Model
{
    protected $table = 'diagnosis';

    protected $fillable = [
        'kunjungan_id',
        'kader_id',
        'cf_kombinasi',
        'status_stunting',
        'rekomendasi',
        'sudah_diverifikasi',
        'verified_by',
        'verified_at',
        'status_override',
        'alasan_override',
        'catatan_bidan',
        'tindak_lanjut',
        'jadwal_kontrol',
    ];

    protected $casts = [
        'cf_kombinasi'      => 'float',
        'sudah_diverifikasi'=> 'boolean',
        'verified_at'       => 'datetime',
        'jadwal_kontrol'    => 'date',
    ];

    // ===== ACCESSORS =====

    // Status final: pakai override bidan kalau ada, kalau tidak pakai hasil sistem
    public function getStatusFinalAttribute(): string
    {
        return $this->status_override ?? $this->status_stunting;
    }

    // Label status dalam Bahasa Indonesia
    public function getLabelStatusAttribute(): string
    {
        return match($this->status_final) {
            'normal'         => 'Normal',
            'berisiko'       => 'Berisiko',
            'stunting'       => 'Stunting',
            'stunting_berat' => 'Stunting Berat',
            default          => '-',
        };
    }

    // Warna untuk UI
    public function getWarnaStatusAttribute(): string
    {
        return match($this->status_final) {
            'stunting_berat' => 'red',
            'stunting'       => 'red',
            'berisiko'       => 'amber',
            'normal'         => 'green',
            default          => 'gray',
        };
    }

    // CF dalam persen
    public function getCfPersenAttribute(): string
    {
        return round($this->cf_kombinasi * 100, 1) . '%';
    }

    // ===== RELATIONSHIPS =====

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }

    public function kader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kader_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailDiagnosis::class, 'diagnosis_id');
    }

    public function detailAktif(): HasMany
    {
        return $this->hasMany(DetailDiagnosis::class, 'diagnosis_id')
                    ->where('gejala_aktif', true);
    }

    // Shortcut ke balita via kunjungan
    public function getBalitaAttribute()
    {
        return $this->kunjungan->balita;
    }

    // ===== SCOPES =====

    public function scopeBelumVerifikasi($query)
    {
        return $query->where('sudah_diverifikasi', false);
    }

    public function scopeSudahVerifikasi($query)
    {
        return $query->where('sudah_diverifikasi', true);
    }

    public function scopeStunting($query)
    {
        return $query->whereIn('status_stunting', ['stunting', 'stunting_berat']);
    }

    public function scopeBerisiko($query)
    {
        return $query->where('status_stunting', 'berisiko');
    }
}


// ===================================================
// Taruh di file terpisah: app/Models/DetailDiagnosis.php
// ===================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailDiagnosis extends Model
{
    protected $table = 'detail_diagnosis';

    protected $fillable = [
        'diagnosis_id',
        'rule_cf_id',
        'gejala_aktif',
        'cf_parsial',
    ];

    protected $casts = [
        'gejala_aktif' => 'boolean',
        'cf_parsial'   => 'float',
    ];

    // ===== RELATIONSHIPS =====

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class, 'diagnosis_id');
    }

    public function ruleCf(): BelongsTo
    {
        return $this->belongsTo(RuleCf::class, 'rule_cf_id');
    }
}
