<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends RootController
{

    private function __construct() {}


    public function index(Request $request)
    {
        $comments = Comment::where('post_id', $request->id)->get();

        $this->sendSuccess('Comments successfully fetched', 'comments', $comments);
    }

    public function store(Request $request) {
        
    }
}
