<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'picture_exists',
        'likes',
        'user_id',
        'tag_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
