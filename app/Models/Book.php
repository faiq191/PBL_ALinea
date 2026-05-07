<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'user_id',
        'type_id',
        'demographic_id',
        'year_id',
        'title',
        'author',
        'description',
        'image'
    ];

    // Relationship to the Year table
    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    // Relationship to the Type table
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    // Relationship to the Demographic table
    public function demographic()
    {
        return $this->belongsTo(Demographic::class);
    }

    // Relationship to the Genre table (Many-to-Many)
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

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
