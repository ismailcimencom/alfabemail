<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Veli extends Model
{
    use HasTenantScope;
    protected $table = 'veliler';
    protected $fillable = [
        'user_id',
        'veli_type',
        'child_email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ogrenciler(): BelongsToMany
    {
        return $this->belongsToMany(Ogrenci::class, 'ogrenci_veli', 'veli_id', 'ogrenci_id');
    }
}
