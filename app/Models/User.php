<?php

namespace App\Models;

use App\Observers\UserObserver;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'email_notifications', 'api_token', 'points'])]
#[Hidden(['password', 'remember_token', 'api_token'])]
#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getLevelAttribute(): string
    {
        return match (true) {
            $this->points >= 600 => 'Finansijski heroj',
            $this->points >= 300 => 'Finansijski ninja',
            $this->points >= 100 => 'Štediša',
            default => 'Početnik',
        };
    }

    public function getNextLevelThresholdAttribute(): ?int
    {
        return match (true) {
            $this->points < 100 => 100,
            $this->points < 300 => 300,
            $this->points < 600 => 600,
            default => null,
        };
    }

    public function getLevelProgressPercentAttribute(): float
    {
        $threshold = $this->next_level_threshold;

        if ($threshold === null) {
            return 100.0;
        }

        $previousThreshold = match ($threshold) {
            100 => 0,
            300 => 100,
            600 => 300,
        };

        $progress = ($this->points - $previousThreshold) / ($threshold - $previousThreshold) * 100;

        return round(min(100, max(0, $progress)), 2);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
