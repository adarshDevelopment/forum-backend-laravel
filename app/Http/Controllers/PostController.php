<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\PostLike;
use Database\Seeders\PostSeeder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Nette\Utils\Random;

class PostController extends RootController
{

    /*
        Remaining:
        1. apply middleware for store, update, deltee and upvote
        2. picture insertion, update and deletion


    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index'])
        ];
    }
    */

    // apply middleware for store, update, and delete and not for index

    private $user;




    public function __construct()
    {
        $this->user = request()->user();
        // $this->middleware('auth:sanctum')->except('index');

    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->first();
        if (!$post) {
            return $this->sendError(statusMessage: 'Post not found', statusCode: 404);
        }

        $comments = $post
            ->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendSuccess('Post successfully fetched', 'post', items: [
            'post' => $post,
            'user' => $post->user,
            'tag' => $post->tag,
            'comments' => $comments
        ]);
    }
    public function index()
    {


        $posts =  Post::orderBy('id', 'desc')->get();



        return $this->sendSuccess('Post successfully retrived', 'posts', $posts);

        // return Post::all();
    }

    public function store(PostRequest $request)
    {

        // return $request->all();
        // remaining: pictures

        // operation for picture 
        /*
            [pictures here]
        */


        // setting tag_id
        if ($request->tag_id < 0) {
            $request->merge(['tag_id' => null]);
        }

        // make slug unique.
        $slug = str_replace(' ', '_',  rtrim($request->title)) . '_' . auth('sanctum')->user()->id;
        $proxySlug = $slug;
        $number = 1;
        $count = 0;
        do {
            $found = false;
            $post = Post::where('slug', $proxySlug)->get();
            // return $post;
            if ($post->isNotEmpty()) {      // if found
                $proxySlug = $slug . '_' . str($number);
                $found = true;
                // return $slug;
                // return $number;
                $count++;
                // return $count;
            } else {
                $found = false;
            }
            $number++;
            // return $number;
        } while ($found == true);

        $slug = $proxySlug;
        $request->merge(['slug' => $slug]);         // adding new property to the request object


        // return $request->all();
        // pictures does not exist for now. likes is set to 0 by default
        // create post
        try {
            // if (!$this->user->posts()->create($request->all())) {
            $post = $this->user->posts()->create([
                'title' => $request->title,
                'slug' => $request->slug,
                'content' => $request->content,
                'tag_id' => $request->tag_id

            ]);
            if (!$post) {
                return $this->sendError('Error createing post!', 500);
            }

            return $this->sendSuccess('Post successfully created', attribute: 'post', items: $post);
        } catch (\Exception $e) {
            return $this->sendError('Error creating post with exception', $e->getMessage(), 500);
        }
    }

    public function update(PostRequest $request)
    {
        // Remaining: pictures

        // if picture does not exist
        $post = $this->user->posts()->where('slug', $request->slug)->first();

        if (!$post) {
            $this->sendError('Post not found', 404);
        }

        $post->update($request->all());
    }



    // Route model binding alternate way. check CommentController destory function
    public function destroy(Request $request)
    {
        // Remaining: delete post pictures

        // only the user who created the post can delete their post and none else 

        /*
        ***** Using gates *****
        $post = $this->user->posts()->where('slug', $request->slug)->first();
        Gate::authorize('authorize-user', $post);
        */

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
        // find the post
        $post = Post::find($request->post_id);
        if (!$post) {
            $this->sendError('Post not found', 404);
        }

        $upvoteStatus = $request->upvoteStatus ? true : false;

        // find existing record
        $likedRecord = PostLike::where('post_id', $post->id)
            ->where('user_id', $this->user->id)
            ->first();

        $result = DB::transaction(function () use ($likedRecord, $post, $upvoteStatus) {

            //  create new entry if no prior entry exists
            if (!$likedRecord) {
                if (!PostLike::create([
                    'upvote_status' => $upvoteStatus,
                    'post_id' => $post->id,
                    'user_id' => $this->user->id,
                    'is_active' => true
                ])) {
                    return false;
                }
            }

            // if prior entry exists and 
            // if user tries to upvote or downvote twice, simply delete the record
            if ($likedRecord->upvote_status == $upvoteStatus) {
                if (!$likedRecord->delete()) {
                    return false;
                }
            }

            // if differnet value comes, change it 
            if (!$likedRecord->update(['upvote_status' => $upvoteStatus])) {
                return false;
            }

            $totalUpvotes = PostLike::where('post_id', $post->id)
                ->where('update_status', true)->count();

            $totalDownVotes = PostLike::where('post_id', $post->id)
                ->where('upvote_status', false)->count();

            $grossTotalVotes = $totalUpvotes - $totalDownVotes;

            if (
                $post->update([
                    'gross_votes' => $grossTotalVotes,
                    'upvotes' => $totalUpvotes,
                    'downvotes' => $totalDownVotes
                ])
            ) {
                return false;
            }
            return true;
        });

        return $result
            ? $this->sendSuccess('Post successfully voted')
            : $this->sendError('Erro votting for post');
    }

    public function getUpvotes() {}
}
