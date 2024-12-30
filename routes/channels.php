<?php

use Illuminate\Support\Facades\Broadcast;

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
