<?php

namespace App\Models;

// Eloquent
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Auth
use Illuminate\Foundation\Auth\User as Authenticatable;

// Notifications
use Illuminate\Notifications\Notifiable;

// Filament
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nickname',
        'name',
        'email',
        'password',
        'avatar_url',
        'mercadolibre_token',
        'mercadolibre_refresh_token',
        'mercadolibre_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'mercadolibre_expires_at' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? $this->avatar_url : 'https://ui-avatars.com/api/?name='.$this->name;
    }

    public function products()
    {
        return $this->hasMany(Product::class); 
    }
}
