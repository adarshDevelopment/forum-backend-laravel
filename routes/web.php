<?php

use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test', function () {
    return 'hello world. working';
});



// Socialite routes

Route::get('auth/google/redirect', [SocialiteController::class, 'googleLogin'])->name('auth.google.redirect');
Route::get('auth/google/callback', [SocialiteController::class, 'googleAuthnetication'])->name('auth.google.callback');
