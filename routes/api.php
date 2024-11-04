<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\SanctumCustomMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Session routes: unprotected 
Route::middleware('auth:sanctum')->prefix('')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});



Route::middleware('sanctumCustomGuest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});




// Post

Route::get('/posts', [PostController::class, 'index']);





/*
    Practice routes
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/middlewareCheck', function (Request $request) {
    return 'middleware working';
})->middleware('sanctumCustom:helloWorld, hello moon');

Route::get('/test', function (Request $request) {
    return response()->json(['hello' => 'world']);
});
