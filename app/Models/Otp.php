<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'type',
        'expires_at',
        'used',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function createForUser(User $user, string $type = 'login'): self
    {
        self::where('user_id', $user->id)
            ->where('type', $type)
            ->where('used', false)
            ->update(['used' => true]);

        return self::create([
            'user_id' => $user->id,
            'code' => self::generateCode(),
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function verify(User $user, string $code, string $type = 'login'): ?self
    {
        $otp = self::where('user_id', $user->id)
            ->where('code', $code)
            ->where('type', $type)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otp) {
            $otp->markAsUsed();
        }

        return $otp;
    }
}
