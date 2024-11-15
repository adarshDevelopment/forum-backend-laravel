<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
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

Route::middleware('sanctumCustomGuest')->group(function () {
    Route::post('/register', action: [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});




// auth:sanctum guarded middleware routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('authCheck', function () {
        return 'auth working';
    });


    // posts routes
    Route::get('post/{slug}', [PostController::class, 'show']);
    Route::post('post', [PostController::class, 'store']);
    Route::put('post', [PostController::class, 'update']);
    Route::delete('post', [PostController::class, 'destroy']);
    Route::post('post/upvote', [PostController::class, 'upvote']);

    // comment routes
    Route::post('/comment', [CommentController::class, 'store']);
    // Route::post('/comment', function(Request $request){return $request->all();});
    Route::put('/comment', [CommentController::class, 'update']);
    Route::delete('/comment', [CommentController::class, 'destroy']);
    Route::post('/comment/upvote', [CommentController::class, 'upvote']);

    // Tag routes
    Route::get('/tag', [TagController::class, 'index']);
});





// Route model binding

Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');


// Post

Route::get('/posts', [PostController::class, 'index']);





/*
    Practice routes
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
