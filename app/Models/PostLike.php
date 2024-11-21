<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $table = 'post_like';
    protected $fillable = [
        'likes',
        'post_id',
        'user_id',
        'upvote_status',
    ];
}
