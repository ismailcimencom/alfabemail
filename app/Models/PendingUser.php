<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'school',
        'veli_type',
        'child_email',
        'verification_code',
        'verification_code_sent_at',
        'email_verified_at',
        'status',
        'rejection_reason',
        'assigned_role',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'verification_code_sent_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isExpired(): bool
    {
        if (!$this->verification_code_sent_at) {
            return true;
        }
        return $this->verification_code_sent_at->addMinutes(15)->isPast();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
