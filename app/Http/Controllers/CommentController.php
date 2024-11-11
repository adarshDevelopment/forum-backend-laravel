<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends RootController
{
    private $user;
    private function __construct()
    {
        $this->user = request()->user();
    }


    /*
        1. store  -> done 
        2. update   -> done 
        3. delete   -> done 
        4. index    ->done
        5. upvote & downvote  -> done 

        Remaining:
            none
            

    */

    public function index(Request $request)
    {
        $comments = Comment::where('post_id', $request->id)->get();

        $this->sendSuccess('Comments successfully fetched', 'comments', $comments);
    }

    public function store(CommentRequest $request)
    {
        // creating record from the currently logged in user
        $comment = $this->user->comments()->create($request->all());
        if (!$comment) {
            return $this->sendError('Error posting comment');
        }

        return $this->sendSuccess('Comment successfully posted');
    }


    public function update(CommentRequest $request)
    {
        $comment = $this->user->comments()->find($request->id);
        if (!$comment) {
            return $this->sendError('Comment not found', 404);
        }
        if (!$comment->update($request->all())) {
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
        $comment = Comment::where('id', $request->comment_id)->first();

        if (!$comment) {
            return $this->sendError('Comment not found', 404);
        }

        $upvoteStatus = $request->upvoteStatus;

        // check for existing record
        $likedRecord = CommentLike
            ::where('user_id', $this->user->id,)
            ->where('comment_id', $comment->id)->first();


        $result = DB::transaction(function () use ($comment, $likedRecord, $upvoteStatus) {
            // no prior records
            if (!$likedRecord) {
                if (CommentLike::create([
                    'upvote_status' => $upvoteStatus,
                    'user_id' => $this->user->id,
                    'comment_id' => $comment->id
                ])) {
                    return false;
                }
            }

            // if prior record exists and 
            // if the user is trying to upvote/downvote twice
            if ($likedRecord->upvote_status == $upvoteStatus) {
                if ($likedRecord->delete()) {
                    return false;
                }
            }

            // if different values:
            if (!$likedRecord->update(['upvote_status' => $upvoteStatus])) {
                return false;
            }

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

            $comment->update([
                'gross_votes' => $grossVotes,
                'upvotes' => $totalDownvotes,
                'downvotes' => $totalDownvotes,
            ]);


            return true;
        });

        return $result
            ? $this->sendSuccess('Comment successfully voted')
            : $this->sendError('Erro voting for comment');
    }
}
