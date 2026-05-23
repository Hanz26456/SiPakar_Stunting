<?php

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

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class, 'diagnosis_id');
    }

    public function ruleCf(): BelongsTo
    {
        return $this->belongsTo(RuleCf::class, 'rule_cf_id');
    }
}
