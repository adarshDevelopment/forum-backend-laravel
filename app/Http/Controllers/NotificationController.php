<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends RootController
{
    public function index($userId)
    {
        $notifications =   Notification::where('notifiable_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        return $this->sendSuccess(statusMessage: 'Notifications fetched successfully.', attribute: 'notifications', items: $notifications);
    }

    public function clickOnNotification(Request $request)
    {
        $notification = Notification::with('post')->find($request->notificationId);
        if (!$notification) {
            $this->sendError(statusMessage: 'Notification not found');
        }
        $notification->is_seen = true;
        if (!$notification->save()) {
            $this->sendError(statusMessage: 'Error updating notification');
        }
        return $this->sendSuccess(statusMessage: 'Notification successfully saved', attribute: 'notification', items: $notification);
    }

    // remove notification icon fucntion 
    // user table should have a notification count field, in which notifications should be added and once clicked, it should be reverted back to 0
}
