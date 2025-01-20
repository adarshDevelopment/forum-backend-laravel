<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('Notification', function () {
    return true;
});

Broadcast::channel('Notification.User.{id}', function ($user, $id) {
    return $user->id === $id;
});


Broadcast::channel('chat', function () {
    return true;
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);
// Broadcast::routes();


Broadcast::channel('update-notification.{userId}', function ($user, $userId) {
    // Log::info(message: 'inside channel. wildcard id: ' . $userId . ' user instane id: ' . $user->id);
    return $user->id === (int) $userId; // $user is the current authenticated user inserted by Laravel
});
