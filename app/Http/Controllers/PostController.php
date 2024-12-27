<?php

namespace App\Http\Controllers;

use App\Events\UpdateNotification;
use App\Http\Requests\PostRequest;
use App\Jobs\DeleteNotificationJob;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostLike;
use Database\Seeders\PostSeeder;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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
        $post = Post::with(['commentsOrdered.user', 'commentsOrdered.commentLike'])->where('slug', $slug)->first();
        if (!$post) {
            return $this->sendError(statusMessage: 'Post not found', statusCode: 404);
        }

        // $comments = $post
        //     ->comments()
        //     ->with('user')
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        // return $comments->count();

        return $this->sendSuccess('Post successfully fetched', 'post', items: [
            'post' => $post,
            'user' => $post->user,
            'tag' => $post->tag,
            // 'comments' => $comments,

        ]);
    }
    public function index()
    {


        $posts =  Post::orderBy('id', 'desc')
            ->with('user')
            ->with('comments')
            ->with('postLike')
            ->get();



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
            return $this->sendError('Error creating post with exception', exceptionMessage: $e->getMessage(), statusCode: 500);
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

        if (!$post->update($request->all())) {
            return $this->sendError('Error updating post',);
        }

        return $this->sendSuccess('Post successfully updated');
    }



    // Route model binding alternate way. check CommentController destory function
    public function destroy(Post $post)
    {

        // Remaining: delete post pictures

        // only the user who created the post can delete their post and none else 


        // ***** Using gates *****
        // $post = $this->user->posts()->where('slug', $request->slug)->first();
        // Gate::authorize('authorize-user', $post);


        if ($this->user->id != $post->user_id) {
            return $this->sendError('The user is not authorized to delete this post.');
        }

        // return 
        if (!$post) {
            return $this->sendError('Post not found', 404);
        }
        /*
                    Delete post pictures here
        */
        if (!$post->delete()) {
            return $this->sendError('Error deleting post');
        }


        return $this->sendSuccess('Post successfully deleted');
    }


    public function upvote(Request $request)
    {

        // function expects post_id/post slug, upvoteStatus
        // slug:slug, user: user.id, upvoteStatus: vote

        if ($this->user->id != $request->user) {        // checking if the user sending the request is the currently logged in user
            return $this->sendError('Unauthorized user', 403);
        }

        // find the post. if not found, send 404
        // $post = Post::find($request->slug);
        $post = Post::where('slug', $request->slug)->first();
        if (!$post) {
            $this->sendError('Post not found', 404);
        }

        $upvoteStatus = $request->upvoteStatus ? true : false;      // downvote or upvote sent from the fontend

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
            // if user tries to upvote or downvote twice, simply delete the record. unlike, un-dislike 
            if ($likedRecord) {
                if ($likedRecord->upvote_status == $upvoteStatus) {
                    if (!$likedRecord->delete()) {
                        return false;
                    }

                    // also delete the notification using Jobs
                    if ($upvoteStatus) {
                        dispatch(function () use ($post) {
                            Notification::where('interactor_user_id', operator: $this->user->id)
                                ->where('post_id', $post->id)
                                ->delete();
                        });
                    }


                    // calculaate upvote and udpat evalues on post table
                    return $this->calculateUpvotes($post);
                }

                // if user has entered a different value 
                if (!$likedRecord->update(['upvote_status' => $upvoteStatus])) {
                    return false;
                }
            }

            // update the notifications table but only when the user upvotes the post
            if ($upvoteStatus) {
                $notificationMessage = $this->getNotificationMessage(interactorUser: $this->user, action: 'liked', attribute: 'post');
                if (!Notification::create([
                    'notification_type' => 'post',

                    'title' => 'New Post Upvote',
                    'message' => $notificationMessage,
                    'interactor_user_id' => $this->user->id,
                    'post_id' => $post->id,

                    'notifiable_id' => $post->user->id,
                ])) {
                    return false;
                }
            }


            return $this->calculateUpvotes(post: $post);            // return true or false depending on the return value of calculateUvptes
        });

        if (!$result) {
            $this->sendError('Error voting for post');
        }
        $post = Post::where('slug', $request->slug)->with('postLike')->first();

        return $this->sendSuccess('Post successfully upvoted. here is the fetched post data', 'updatedPost', items: $post);
    }


    //  THIS FUNCTION counts the total upvotes/downvotes records from post_like table and posts them into the post table
    function calculateUpvotes($post)
    {

        try {
            // fetching total true vlaue upvotes from post_like table 
            $totalUpvotes = PostLike::where('post_id', $post->id)
                ->where('upvote_status', true)->count();

            $totalDownVotes = PostLike::where('post_id', $post->id)     // fetching total false value downvotes from post_like table
                ->where('upvote_status', false)->count();

            $grossTotalVotes = $totalUpvotes - $totalDownVotes;

            if (
                !$post->update([
                    'gross_votes' => $grossTotalVotes,
                    'upvotes' => $totalUpvotes,
                    'downvotes' => $totalDownVotes
                ])
            ) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }



    public function getUpvotes($slug)
    {

        $post = Post::where('slug', $slug)->first();


        $user =  Auth('sanctum')->user();
        //  if user not logged in, dont send the user's votes on the picture_user value;
        if (!$user) {
            return $this->sendSuccess('Upvotes for post successfully fetched', 'post', ['post' => $post]);
        }
        $postLike = PostLike::where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();
        // return $postLike;

        if (!$post) {
            return $this->sendError('No such post found', 404);
        }
        return $this->sendSuccess('Upvotes for post successfully fetched', 'post', ['post' => $post, 'status' => $postLike]);
    }
}
