<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'is_admin',
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
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === 1;
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function customNotifications()
    {
        return $this->hasMany(CustomNotification::class)->latest();
    }

    public function unreadCustomNotificationsCount()
    {
        return $this->customNotifications()->where('is_read', false)->count();
    }
}
