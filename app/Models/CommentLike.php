<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    protected $table = 'likes';
    protected $fillable = [
        'likes',
        'comment_id',
        'user_id'
    ];
}
