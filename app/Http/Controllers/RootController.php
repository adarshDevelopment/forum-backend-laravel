<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class RootController extends Controller
{


    public $notification;
    /*
        title: notification title (new comment, new post upvote)
        user: the notifiable user
        action: liked, upvoted, 
        attribute: post or comment
        post: the post instance
        message instance: the user has liked you comment 

        IS NOT USED ANYMORE
     */


    public function getNotificationList(
        string $title,
        User $interactor_user,
        string $action = 'liked',
        string $attribute,
        $post = null
    ): array {
        return [
            'title' => $title,
            'message' => "{$interactor_user->name} has {$action} your {$attribute}",
            'interactor_user' => $interactor_user,
            'post' => $post

        ];
    }


    public function getNotificationMessage(
        User $interactorUser,
        string $action = 'liked',
        string $attribute
    ){
        return "{$interactorUser->name} has {$action} your {$attribute}";
    }



    public function sendError($statusMessage, $statusCode = 500, $exceptionMessage = '',)
    {
        // if exception message does not exist, dont send key value
        if (!$exceptionMessage) {
            return response()->json([
                'status' =>  false,
                'message' => $statusMessage,
            ], $statusCode);
        }

        return response()->json([
            'status' =>  false,
            'message' => $statusMessage,
            'exceptionMessage' => $exceptionMessage
        ], $statusCode);
    }

    public function sendSuccess($statusMessage, $attribute = '', $items = [])
    {

        // if attribute is empty, dont send the empty array

        return $attribute ?
            response()->json([
                'status' =>  true,
                'message' => $statusMessage,
                $attribute => $items
            ], 200)
            :
            response()->json([
                'status' =>  true,
                'message' => $statusMessage,
            ], 200);
    }
}
