<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //

    protected $table = 'notifications';

    protected $fillable = [
        'notification_type',

        // 'data',
        'title',
        'message',
        'interactor_user_id',
        'post_id',
        'comment_id',

        'notifiable_id',
        'is_seen',
        'is_read',
        'read_at'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
