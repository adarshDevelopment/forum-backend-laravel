<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentController extends RootController
{
    private $user;
    public function __construct()
    {
        $this->user = auth('sanctum')->user();

        if ($this->user) {      // doing this will reutrn elequoent instance which can be used to call in realationships functions, unlike before
            $this->user = User::find($this->user->id);
        }
    }


    /*
        Remaining:
            none
        
    */

    public function index($slug)
    {
        $post = Post::where('slug', $slug)->first();

        $comments = Comment::where('post_id', $post->id)
            ->with('user')
            ->with('commentLike')
            ->orderBy('created_at', 'desc')
            ->get();


        // return $comments;
        // return $comments;
        return $this->sendSuccess('Comments successfully fetched', 'comments', $comments);
    }

    public function store(CommentRequest $request)
    {
        // return $request->all();
        $post = Post::where('slug', $request->slug)->first();
        if (!$post) {
            return $this->sendError('Post not found');
        }

        // return $request->all(); 
        // creating record from the currently logged in user=
        // return $this->user;
        // return $this->user->comments;

        // return $request->user()->comments()->create($request->all());
        $comment = $this->user->comments()->create([
            'comment' => $request->comment,
            'post_id' => $post->id,
            // 'user_id' => $this->user->id
        ]);

        if (!$comment) {
            return $this->sendError('Error posting comment');
        }

        return $this->sendSuccess('Comment successfully posted');
    }


    public function update(CommentRequest $request, $id)
    {
        // return $request->all();
        $comment = $this->user->comments->find($id);
        if (!$comment) {
            return $this->sendError('Comment not found', 404);
        }
        $comment->comment = $request->comment;
        if (!$comment->update()) {
            return $this->sendError('Error editing comment');
        }
        return $this->sendSuccess('Comment successfully deleted');
    }

    // for alternate way of route model binding, check  PostController destroy function
    public function destroy(Comment $comment)
    {

        if ($comment->user_id !== $this->user->id) {
            return $this->sendError('Unauthorized user', 403);
        }

        if (!$comment->delete()) {
            return $this->sendError('Error deleting comment');
        }

        return $this->sendSuccess('Comment successfully deleted');
    }




    public function upvote(Request $request)
    {
        $comment = Comment::where('id', $request->comment_id)->with('commentLike')->first();
        if (!$comment) {
            return $this->sendError('Comment not found', 404);
        }

        $upvoteStatus = $request->upvoteStatus;
        // check for existing record
        $likedRecord = CommentLike
            ::where('user_id', $this->user->id,)
            ->where('comment_id', $comment->id)->first();

        $result = DB::transaction(function () use ($comment, $likedRecord, $upvoteStatus) {
            // no prior records, create one 
            try {
                if (!$likedRecord) {
                    if (!CommentLike::create([
                        'upvote_status' => $upvoteStatus,
                        'user_id' => $this->user->id,
                        'comment_id' => $comment->id
                    ])) {
                        return false;
                    }
                    $result = $this->calcualteUpvotes($comment);
                    return $result;
                }
            } catch (\Exception $e) {
                return false;
            }

            // if prior record exists and 
            // if the user is trying to upvote/downvote twice
            if ($likedRecord->upvote_status == $upvoteStatus) {
                if (!$likedRecord->delete()) {
                    return false;
                }
            } else {
                // if not same value, update and calcualte upvotes once again
                if (!$likedRecord->update(['upvote_status' => $upvoteStatus])) {
                    return false;
                }
            }

            return $this->calcualteUpvotes($comment);
        });

        // fetching the new updated value of comment_like for fron-end
        $comment = Comment::where('id', $request->comment_id)->with('commentLike')->first();

        return $result
            ? $this->sendSuccess('Comment successfully voted', 'updatedComment', $comment)
            : $this->sendError('Error voting for comment');
    }

    public function calcualteUpvotes($comment)
    {
        try {
            // also save upvotes and downvotes on comments table... fetching total downvotes, upvotes and gross votes to insert it to the comment instance of the selected comment
            $totalUpvotes = CommentLike
                ::where('comment_id', $comment->id)
                ->where('upvote_status', true)
                ->count();

            $totalDownvotes = CommentLike
                ::where('comment_id', $comment->id)
                ->where('upvote_status', false)
                ->count();

            $grossVotes = $totalUpvotes - $totalDownvotes;

            Log::info('total upvotes: ' . $totalUpvotes . ' Total downvotes: ' . $totalDownvotes . ' gross votes: ' . $grossVotes);
            $comment->update([
                'gross_votes' => $grossVotes,
                'upvotes' => $totalUpvotes,
                'downvotes' => $totalDownvotes,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCommnetUpvotes($id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return $this->sendError('Comment not found', 404);
        }
        $user = Auth::guard('sanctum')->user();
        if (!$user) {   // if user not logged in, jsut send the upvotes and nothing else 
            return $this->sendSuccess('Upvotes successfully fetched for the requested comment', 'comment', $comment);
        }
        // $likedComment = CommentLike::where('comment_id', $comment->id);
        return $comment;
    }
}
