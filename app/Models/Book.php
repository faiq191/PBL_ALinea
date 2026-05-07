<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
       protected $fillable = [
        'title',
        'author',
        'image',
        'user_id',
        'genre'
    ];

        public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function isAvailable()
    {
        return !$this->loans()->whereIn('status', ['pending', 'dipinjam'])->exists();
    }

}
