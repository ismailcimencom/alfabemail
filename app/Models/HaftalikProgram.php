<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HaftalikProgram extends Model
{
    protected $table = 'haftalik_programlar';

    protected $fillable = [
        'okul_id',
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

    protected static function booted()
    {
        static::creating(function ($program) {
            if (empty($program->okul_id)) {
                $program->okul_id = auth()->user()?->okul?->id
                    ?? Okul::where('yonetici_user_id', auth()->id())->first()?->id;
            }
        });
    }

    public function okul(): BelongsTo
    {
        return $this->belongsTo(Okul::class);
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
