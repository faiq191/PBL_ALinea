<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    protected $fillable = [
        'reporter_id',
        'reported_id',
        'reported_type',
        'reason',
        'discussion_id',
        'comment_id',
        'status',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reported()
    {
        return $this->belongsTo(User::class, 'reported_id');
    }

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
