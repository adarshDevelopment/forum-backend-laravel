<?php

use App\Events\ChatEvent;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Middleware\SanctumCustomMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





// Session routes: protected 
Route::middleware('auth:sanctum')->prefix('')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('authCheck', function () {
        return 'auth working';
    });
});

// guest only routes
Route::middleware('sanctumCustomGuest')->group(function () {
    Route::post('/register', action: [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});


// open routes 

Route::get('/posts', [PostController::class, 'index']);
Route::get('post/{slug}', [PostController::class, 'show']);


Route::get('/comment/{slug}', [CommentController::class, 'index']);

Route::get('post/upvoteStatus/{slug}', [PostController::class, 'getUpvoteStatus']);       // get single post along with all its fields 
// end of open routes



// auth:sanctum guarded middleware routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('authCheck', function () {
        return 'auth working';
    });

    // posts routes
    Route::post('post', [PostController::class, 'store']);
    Route::put('post', [PostController::class, 'update']);
    Route::delete('post/{post}', [PostController::class, 'destroy']);
    Route::post('post/upvote', [PostController::class, 'upvote']);

    // comment routes
    Route::post('/comment', [CommentController::class, 'store']);
    // Route::post('/comment', function(Request $request){return $request->all();});
    Route::put('/comment/{id}', [CommentController::class, 'update']);
    Route::delete('/comment/{comment}', [CommentController::class, 'destroy']);
    Route::post('/comment/upvote', [CommentController::class, 'upvote']);

    // Tag routes
    Route::get('/tag', [TagController::class, 'index']);

    // Notification route
    Route::get('/notification/{userId}', [NotificationController::class, 'index']);
    Route::post('/resetNotificationCount', [NotificationController::class, 'resetNotificationCount']);

    Route::post('/markNotificationAsRead', [NotificationController::class, 'markNotificationAsRead']);

    // Profile routes
    


    // Email verification routes:

    // code to resend verificaiton code
    Route::post('resendVerification', [AuthController::class, 'resendEmailVerification'])->middleware('throttle:6,1');
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify')->withoutMiddleware('auth:sanctum');
});




// Route model binding

// Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');


// Post


############################################################################################################################# ###########################################################################

/*
    Test routes
*/

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::get('/middlewareCheck', function (Request $request) {
    return 'middleware working';
})->middleware('sanctumCustom:helloWorld, hello moon');

Route::get('/test', function (Request $request) {
    return response()->json(['hello' => 'world']);
});

Route::get('/broadcast', function () {
    // event(new ChatEvent('API john cena'));
    dispatch(function () {
        event(new ChatEvent('API john cena'));
    });

    // broadcast(new ChatEvent('API john cena'));
    return 'event executed';
});
