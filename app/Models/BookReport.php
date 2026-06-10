<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookReport extends Model
{
    protected $fillable = [
        'reporter_id',
        'book_id',
        'reason',
        'status',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
