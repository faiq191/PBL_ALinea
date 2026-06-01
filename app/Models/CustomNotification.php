<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomNotification extends Model
{
    protected $table = 'custom_notifications';

    protected $fillable = [
        'user_id',
        'sender_id',
        'title',
        'content',
        'link',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public static function send($userId, $title, $content, $link = null, $senderId = null)
    {
        $notification = self::create([
            'user_id'   => $userId,
            'sender_id' => $senderId,
            'title'     => $title,
            'content'   => $content,
            'link'      => $link,
            'is_read'   => false,
        ]);

        try {
            broadcast(new \App\Events\NotificationSent($notification));
        } catch (\Exception $e) {
            \Log::warning('Reverb notification broadcast failed: ' . $e->getMessage());
        }

        return $notification;
    }
}
