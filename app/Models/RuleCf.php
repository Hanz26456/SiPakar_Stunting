<?php

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
        'is_active' => 'boolean',
    ];

    public function getCfPakarAttribute(): float
    {
        return round($this->mb - $this->md, 4);
    }

    public function gejala(): BelongsTo
    {
        return $this->belongsTo(Gejala::class, 'gejala_id');
    }

    public function detailDiagnosis(): HasMany
    {
        return $this->hasMany(DetailDiagnosis::class, 'rule_cf_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }
}
