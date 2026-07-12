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

