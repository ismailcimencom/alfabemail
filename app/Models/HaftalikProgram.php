<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HaftalikProgram extends Model
{
    protected $table = 'haftalik_programlar';

    protected $fillable = [
        'sinif_id',
        'gun',
        'baslangic',
        'bitis',
        'ders_adi',
        'ogretmen_id',
    ];

    protected function casts(): array
    {
        return [
            'baslangic' => 'datetime:H:i',
            'bitis' => 'datetime:H:i',
        ];
    }

    public function sinif(): BelongsTo
    {
        return $this->belongsTo(Sinif::class);
    }

    public function ogretmen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ogretmen_id');
    }
}
