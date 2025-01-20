<?php

namespace App\Events;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    // public $notification;

    public function __construct(public $notification, public $user)
    {
        $this->notification = $notification;
        $this->user = $user;

        // Log::debug('inside UpdateNotification notification count ' . $this->notificationCount);
        // Log::debug('inside UpdateNotification notification: ' . $this->notification);
    }

    public function broadcastWith()
    {
        return [
            'notification' => $this->notification,
            // 'hello' => 'world'
        ];
    }

    public function broadcastOn(): array
    {
        // Log::info('inside UpdateNotification broadcastOn. user id: ' . $this->user->id);
        return [
            new PrivateChannel('update-notification.' . $this->notification->notifiable_id),
        ];
    }
}
