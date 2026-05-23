<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gejala extends Model
{
    protected $table = 'gejala';

    protected $fillable = [
        'kode',
        'nama_gejala',
        'kategori',
        'deskripsi',
        'sumber',
        'kolom_sumber',
        'operator',
        'nilai_threshold',
        'is_active',
    ];

    protected $casts = [
        'nilai_threshold' => 'float',
        'is_active'       => 'boolean',
    ];

    // ===== RELATIONSHIPS =====

    public function rules(): HasMany
    {
        return $this->hasMany(RuleCf::class, 'gejala_id');
    }

    public function rulesAktif(): HasMany
    {
        return $this->hasMany(RuleCf::class, 'gejala_id')
                    ->where('is_active', true);
    }

    // ===== SCOPES =====

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOtomatis($query)
    {
        return $query->where('sumber', 'otomatis');
    }

    public function scopeManual($query)
    {
        return $query->where('sumber', 'manual');
    }
}


// ===================================================
// Taruh di file terpisah: app/Models/RuleCf.php
// ===================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RuleCf extends Model
{
    protected $table = 'rule_cf';

    protected $fillable = [
        'gejala_id',
        'kode_rule',
        'kondisi',
        'status_diagnosa',
        'mb',
        'md',
        'is_active',
    ];

    protected $casts = [
        'mb'        => 'float',
        'md'        => 'float',
        'cf_pakar'  => 'float',
        'is_active' => 'boolean',
    ];

    // CF pakar = MB - MD (stored as di DB, tapi kita sediakan juga accessor)
    public function getCfPakarAttribute(): float
    {
        return round($this->mb - $this->md, 4);
    }

    // ===== RELATIONSHIPS =====

    public function gejala(): BelongsTo
    {
        return $this->belongsTo(Gejala::class, 'gejala_id');
    }

    public function detailDiagnosis(): HasMany
    {
        return $this->hasMany(DetailDiagnosis::class, 'rule_cf_id');
    }

    // ===== SCOPES =====

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
