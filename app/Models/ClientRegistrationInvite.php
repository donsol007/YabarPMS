<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ClientRegistrationInvite extends Model
{
    protected $fillable = ['token', 'expires_at', 'used_at', 'created_by'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hasExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function isActive(): bool
    {
        return ! $this->hasExpired() && ! $this->isUsed();
    }

    public static function issue(?int $createdBy = null): self
    {
        return static::create([
            'token' => Str::random(40),
            'expires_at' => now()->addDay(),
            'created_by' => $createdBy,
        ]);
    }
}
