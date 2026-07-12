<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'okul_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin') && $this->is_active;
        }
        if ($panel->getId() === 'yonetici') {
            return $this->hasRole('yonetici') && $this->is_active;
        }
        if ($panel->getId() === 'ogretmen') {
            return $this->hasRole('ogretmen') && $this->is_active;
        }
        if ($panel->getId() === 'veli') {
            return $this->hasRole('veli') && $this->is_active;
        }
        return false;
    }

    public function adminApprovalsRequested(): HasMany
    {
        return $this->hasMany(AdminApproval::class, 'target_user_id');
    }

    public function adminApprovalsGiven(): HasMany
    {
        return $this->hasMany(AdminApproval::class, 'approver_user_id');
    }

    public function isFullyApproved(): bool
    {
        if (!$this->hasRole('admin')) {
            return true;
        }
        $totalApprovals = $this->adminApprovalsRequested()->count();
        if ($totalApprovals === 0) {
            return true;
        }
        return $this->adminApprovalsRequested()
            ->whereNotNull('approved_at')
            ->count() === $totalApprovals;
    }

    public function okul(): HasOne
    {
        return $this->hasOne(Okul::class, 'yonetici_user_id');
    }

    public function bagli_okul(): BelongsTo
    {
        return $this->belongsTo(Okul::class, 'okul_id');
    }

    public function ogrenci(): HasOne
    {
        return $this->hasOne(Ogrenci::class);
    }

    public function veli(): HasOne
    {
        return $this->hasOne(Veli::class);
    }

    public function ogretmen_siniflar(): HasMany
    {
        return $this->hasMany(Sinif::class, 'ogretmen_user_id');
    }

    public function ogretmen_sinifler_pivot(): BelongsToMany
    {
        return $this->belongsToMany(Sinif::class, 'ogretmen_sinif', 'ogretmen_user_id', 'sinif_id');
    }

    public function olusturulan_odevler(): HasMany
    {
        return $this->hasMany(Odev::class, 'ogretmen_id');
    }
}
