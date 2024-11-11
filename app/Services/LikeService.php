<?php

namespace App\Services;

class LikeService
{

    protected $user;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $user = request()->user();
    }



    public function upvote(object $class, $table)
    {
        $like = $class::where('post_id', $table->id)
            ->where('user_id', $this->user->id)
            ->first();

        //  new entry
        if (!$like) {
            $class::create([
                'likes' => 1,
                'post_id' => $table->id,
                'user_id' => $this->user->id,
                'is_active' => true

            ]);
        }

        // increment like
        $like->likes++;

        
        if (!$like->update()) {
            return false;
        }
    }
}
