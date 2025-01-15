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


    public Notification $notification;

    public function __construct(Notification $notification, public $user)
    {
        $this->notification = $notification;
        $this->user = $user;

        // Log::info('inside UpdateNotification constructor. user->id: ' . $this->user->id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */

    public function broadcastWith()
    {
        return [
            'notification' => $this->notification
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
