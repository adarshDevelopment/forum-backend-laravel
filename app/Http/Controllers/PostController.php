<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Database\Seeders\PostSeeder;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class PostController extends RootController
{
    // apply middleware for store, update, and delete and not for index

    public function index()
    {


        $posts =  Post::orderBy('id', 'desc')->get();

        return $this->sendSuccess('Post successfully retrived', 'posts', $posts);

        // return Post::all();
    }

    public function store(PostRequest $request)
    {
        // remaining: pictures

        // operation for picture 
        /*
            [pictures here]
        */

        // make slug unique. 

        // slug part   replace spac e with _
        $slug = $request->title . '_' . $request->user()->id;

        do {
            $found = false;
            $number = 1;

            $post = Post::where('slug', $slug)->get();
            if ($post->isNotEmpty()) {
                $slug = $slug . '_' . $number;
                $found = true;
            } else {
                $found = false;
            }
            $number++;
        } while ($found == true);

        $request->merge(['slug' => $slug]);         // adding new property to the request object



        // pictures does not exist for now. likes is set to 0 by default
        // create post
        if (!Post::create($request->all())) {
            $this->sendError('Error createing post!', 500);
        }

        $this->sendSuccess('Post successfully created');
    }

    public function update(PostRequest $request)
    {
        // Remaining: pictures

        // if picture does not exist
        $post = Post::where('slug', $request->slug)->first();

        if (!$post) {
            $this->sendError('Post not found', 404);
        }

        $post->update($request->all());
    }


    public function destroy(Request $request)
    {
        // Remaining: delete post pictures

        $post = Post::where('slug', $request->slug)->first();
        if (!$post) {
            $this->sendError('Post not found', 404);
        }

        /*
                    Delete post pictures here
        */


        if (!$post->delete()) {
            $this->sendError('Error deleting post');
        }


        $this->sendSuccess('Post successfully deleted');
    }


    public function upvote(Request $request)
    {
        $post = Post::where('slug', $request->slug)->first();
        if (!$post) {
            $this->sendError('Post not found', 404);
        }
        $post->likes++;

        if (!$post->update()) {
            $this->sendError('Error upvoting post');
        }
        $this->sendSuccess('Post successfully upvoted');
    }

    public function downVote(Request $request)
    {
        $post = Post::where('slug', $request->slug)->first();
        if (!$post) {
            $this->sendError('Post not found', 404);
        }
        $post->likes--;

        if (!$post->update()) {
            $this->sendError('Error upvoting post');
        }
        $this->sendSuccess('Post successfully downvoted');
    }
}
