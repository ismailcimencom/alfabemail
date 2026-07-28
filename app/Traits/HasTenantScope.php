<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasTenantScope
{
    public function scopeForCurrentUser(Builder $query): Builder
    {
        $user = auth()->user();

        if (!$user) {
            return $query;
        }

        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole('ogretmen')) {
            return $this->scopeForOgretmen($query, $user);
        }

        if ($user->hasRole('veli')) {
            return $this->scopeForVeli($query, $user);
        }

        if ($user->hasRole('ogrenci')) {
            return $this->scopeForOgrenci($query, $user);
        }

        return $query;
    }

    protected function scopeForOgretmen(Builder $query, User $user): Builder
    {
        return match(static::class) {
            \App\Models\Ogrenci::class => $query->whereHas('sinif', function($q) use ($user) {
                $q->whereHas('ogretmenler', fn($q) => $q->where('ogretmen_user_id', $user->id));
            }),
            \App\Models\Sinif::class => $query->whereHas('ogretmenler', fn($q) => $q->where('ogretmen_user_id', $user->id)),
            default => $query->whereRaw('1 = 0'),
        };
    }

    protected function scopeForVeli(Builder $query, User $user): Builder
    {
        $veli = $user->veli;

        if (!$veli) {
            return $query->whereRaw('1 = 0');
        }

        return match(static::class) {
            \App\Models\Ogrenci::class => $query->whereHas('veliler', fn($q) => $q->where('veli_id', $veli->id)),
            default => $query->whereRaw('1 = 0'),
        };
    }

    protected function scopeForOgrenci(Builder $query, User $user): Builder
    {
        $ogrenci = $user->ogrenci;

        if (!$ogrenci) {
            return $query->whereRaw('1 = 0');
        }

        return match(static::class) {
            \App\Models\Ogrenci::class => $query->where('id', $ogrenci->id),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
