<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    public const TYPE_GUEST = 0;
    public const TYPE_USER = 1;
    public const TYPE_ADMIN = 2;

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            self::TYPE_GUEST => 'Guest',
            self::TYPE_USER => 'User',
            self::TYPE_ADMIN => 'Admin',
            default => 'Unknown',
        };
    }

    public function isAdmin()
    {
        return $this->type === self::TYPE_ADMIN;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'type',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function challengeAreas()
    {
        return $this->belongsToMany(ChallengeArea::class, 'user_challenge_area')
            ->withTimestamps();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
