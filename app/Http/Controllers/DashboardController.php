<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends RootController
{

    public function __construct() {}

    /**
     * accepts id of the user and returns post and comment of the associated user
     * change it to userName when username setup is done in socialite
     */
    public function index($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError(statusMessage: 'User not found', statusCode: 404);
        }

        $posts = $user->posts()->orderBy('created_at', 'desc')->get();
        unset($user->posts);

        $comments = $user->comments()->with(['post:id,slug,title,content'])->orderBy('created_at', 'desc')->get();

        return $this->sendSuccess('Dashboard data fetched', 'data', [
            'posts' => $posts,
            'comments' => $comments,
            'user' => $user
        ]);
    }
}
