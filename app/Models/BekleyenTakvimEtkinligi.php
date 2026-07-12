<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BekleyenTakvimEtkinligi extends Model
{
    protected $table = 'bekleyen_takvim_etkinlikleri';

    protected $fillable = [
        'ogrenci_id',
        'odev_id',
        'baslik',
        'aciklama',
        'teslim_tarihi',
        'eklendi_mi',
        'hata_mi',
        'hata_mesaji',
    ];

    protected function casts(): array
    {
        return [
            'teslim_tarihi' => 'date',
            'eklendi_mi' => 'boolean',
            'hata_mi' => 'boolean',
        ];
    }

    public function ogrenci(): BelongsTo
    {
        return $this->belongsTo(Ogrenci::class);
    }

    public function odev(): BelongsTo
    {
        return $this->belongsTo(Odev::class);
    }
}
