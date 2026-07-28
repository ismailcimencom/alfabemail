<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sinif extends Model
{
    // use HasTenantScope;
    protected $table = 'siniflar';
    protected $fillable = [
        'ad',
        'durum',
    ];

    protected static function booted()
    {
        static::created(function ($sinif) {
            if (request()->has('ogretmenler') && is_array(request('ogretmenler'))) {
                $sinif->ogretmenler()->sync(request('ogretmenler'));
            }
        });
    }

    public function syncOgretmenler(array $ogretmenIds): void
    {
        $this->ogretmenler()->sync($ogretmenIds);
    }

    public function ogretmenler(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ogretmen_sinif', 'sinif_id', 'ogretmen_user_id')
            ->withPivot('brans')
            ->withTimestamps();
    }

    public function ogrenciler(): HasMany
    {
        return $this->hasMany(Ogrenci::class);
    }

    public function odevler(): HasMany
    {
        return $this->hasMany(Odev::class);
    }

    public function haftalikProgramlar(): HasMany
    {
        return $this->hasMany(HaftalikProgram::class);
    }
}
