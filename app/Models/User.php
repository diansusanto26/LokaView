<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'profile_picture',
        'name',
        'email',
        'password',
        'coins',
        'role'

    ];

    protected $casts = [
        'coins' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function topUps()
    {
        return $this->hasMany(CoinTopUp::class);
    }

    public function unlockedEpisodes()
    {
        return $this->hasMany(UnlockedEpisode::class);
    }

    public function WatchProgress()
    {
        return $this->hasMany(WatchProgress::class);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->wallet()->create();
        });
    }
}
