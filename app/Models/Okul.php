<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Okul extends Model
{
    use HasTenantScope;
    protected $table = 'okullar';
    protected $fillable = [
        'yonetici_user_id',
        'ad',
        'adres',
        'telefon',
        'is_active',
        'yonetici_ad_soyad',
        'yonetici_email',
        'ulke',
        'sehir',
        'ilce',
        'mahalle',
        'durum',
        'red_nedeni',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function yonetici(): BelongsTo
    {
        return $this->belongsTo(User::class, 'yonetici_user_id');
    }

    public function siniflar(): HasMany
    {
        return $this->hasMany(Sinif::class);
    }

    public function haftalikProgramlar(): HasMany
    {
        return $this->hasMany(HaftalikProgram::class);
    }
}
