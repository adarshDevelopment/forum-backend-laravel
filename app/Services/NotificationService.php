<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;

class NotificationService
{
    public $notification;


    /*
        title: notification title (new comment, new post upvote)
        user: the notifiable user
        action: liked, upvoted, 
        attribute: post or comment
        post: the post instance
        message instance: the user has liked you comment 
    */

    public function __construct(public string $title, public User $interactor_user, public string $action = 'liked', public string $attribute, public $post = null)
    {

        $this->notification = [
            'title' => $title,
            'message' => "{$this->interactor_user->name} has {$this->action} your {$this->attribute}",
            'interactor_user' => $this->interactor_user,
            'post' => $this->post

        ];
    }

    public function sendNotification(){
        
    }
}
