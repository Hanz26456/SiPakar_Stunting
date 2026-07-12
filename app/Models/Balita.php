<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Balita extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'balita';

    protected $fillable = [
        'nama',
        'tanggal_lahir',
        'jenis_kelamin',
        'nama_ibu',
        'nama_ayah',
        'no_hp_ortu',
        'alamat',
        'rt_rw',
        'desa',
        'no_kk',
        'user_id',
        'kader_id',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active'     => 'boolean',
    ];

    // ===== ACCESSORS =====

    // Hitung usia dalam bulan
    public function getUsiaBulanAttribute(): int
    {
        return (int) Carbon::parse($this->tanggal_lahir)
            ->diffInMonths(now());
    }

    // Format usia: "18 bulan" atau "1 tahun 6 bulan"
    public function getUsiaFormatAttribute(): string
    {
        $bulan = $this->usia_bulan;
        if ($bulan < 12) {
            return "{$bulan} bulan";
        }
        $tahun = intdiv($bulan, 12);
        $sisaBulan = $bulan % 12;
        return $sisaBulan > 0
            ? "{$tahun} tahun {$sisaBulan} bulan"
            : "{$tahun} tahun";
    }

    // Jenis kelamin lengkap
    public function getJenisKelaminLengkapAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    // Status stunting terbaru (dari diagnosis terakhir)
   public function getStatusTerbaruAttribute(): ?string
{
    return $this->kunjunganTerbaru?->diagnosis?->status_final;
}

    // ===== RELATIONSHIPS =====

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kader_id');
    }

    public function riwayat(): HasOne
    {
        return $this->hasOne(RiwayatBalita::class, 'balita_id');
    }

    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'balita_id')
                    ->orderBy('tanggal_kunjungan', 'desc');
    }

    public function kunjunganTerbaru(): HasOne
    {
        return $this->hasOne(Kunjungan::class, 'balita_id')
                    ->latestOfMany('tanggal_kunjungan');
    }


    public function diagnosis(): HasMany
    {
        return $this->hasManyThrough(
            Diagnosis::class,
            Kunjungan::class,
            'balita_id',
            'kunjungan_id'
        );
    }


    // ===== SCOPES =====

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePerempuan($query)
    {
        return $query->where('jenis_kelamin', 'P');
    }

    public function scopeLakiLaki($query)
    {
        return $query->where('jenis_kelamin', 'L');
    }
}
