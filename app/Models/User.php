<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
        'foto',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ===== ROLE HELPERS =====

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBidan(): bool
    {
        return $this->role === 'bidan';
    }

    public function isKader(): bool
    {
        return $this->role === 'kader';
    }

    public function isOrtu(): bool
    {
        return $this->role === 'ortu';
    }

    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    // ===== RELATIONSHIPS =====

    // Balita yang didaftarkan user ini (untuk ortu)
    public function balita(): HasMany
    {
        return $this->hasMany(Balita::class, 'user_id');
    }

    // Kunjungan yang dicatat kader ini
    public function kunjunganDicatat(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'kader_id');
    }

    // Diagnosis yang diverifikasi bidan ini
    public function diagnosisVerifikasi(): HasMany
    {
        return $this->hasMany(Diagnosis::class, 'verified_by');
    }

    // ===== SCOPES =====

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
