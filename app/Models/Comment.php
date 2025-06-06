<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'comment',
        'gross_votes',
        'upvotes',
        'downvotes',
        'post_id',
        'user_id',
        'is_active',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentLike()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
