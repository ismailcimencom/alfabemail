<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Services\MailcowService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ogrenci extends Model
{
    use HasTenantScope;
    protected $table = 'ogrenciler';
    protected $fillable = [
        'user_id',
        'sinif_id',
        'mailbox_local_part',
        'mailbox_quota_mb',
        'qr_token',
        'qr_svg',
        'anne_email',
        'baba_email',
        'veli_email',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ogrenci) {
            if (empty($ogrenci->user_id)) {
                $ogrenci->user_id = auth()->id();
            }
        });

        static::deleting(function ($ogrenci) {
            if (!auth()->check() || !auth()->user()->can('mailbox.delete')) {
                return;
            }

            if ($ogrenci->mailbox_local_part) {
                try {
                    $mailcow = app(MailcowService::class);
                    $email = $ogrenci->mailbox_local_part . '@' . config('mailcow.domain', 'alfabe.co');
                    $mailcow->deleteMailbox($email);
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Uyarı')
                        ->body('Mailbox silinirken hata: ' . $e->getMessage())
                        ->warning()
                        ->send();
                }
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sinif(): BelongsTo
    {
        return $this->belongsTo(Sinif::class);
    }

    public function veliler(): BelongsToMany
    {
        return $this->belongsToMany(Veli::class, 'ogrenci_veli', 'ogrenci_id', 'veli_id');
    }

    public function odevler(): BelongsToMany
    {
        return $this->belongsToMany(Odev::class, 'odev_ogrenci', 'ogrenci_id', 'odev_id')
            ->withPivot('tamamlandi', 'tamamlanma_tarihi')
            ->withTimestamps();
    }

    public function bekleyenTakvimEtkinlikleri(): HasMany
    {
        return $this->hasMany(BekleyenTakvimEtkinligi::class, 'ogrenci_id');
    }
}
