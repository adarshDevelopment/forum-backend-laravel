<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    protected $table = 'comment_like';
    protected $fillable = [
        'comment_id',
        'user_id',
        'upvote_status'
    ];
}
