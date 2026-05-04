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
}
