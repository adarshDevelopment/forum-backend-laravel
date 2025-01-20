<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends RootController
{
    /*
        expects userID
        returns notification and the number of unseen notifications
    */
    public function index($userId)
    {

        $query = Notification::with(
            ['post' => function ($query) {
                $query->select('id', 'slug');
            }]
        )
            ->where('notifiable_id', $userId)
            ->orderBy('created_at', 'desc')->limit(10);

        $notifications = $query->limit(10)->get();

        $unseenNotifications = $query->where('is_seen', false)->count();
        // limit querynotifications to 20
        // and count where is_seen is false

        return $this->sendSuccess(
            statusMessage: 'Notifications fetched successfully.',
            attribute: 'notifications',
            items: [
                'notifications' => $notifications,
                'unseenNotifications' => $unseenNotifications
            ]
        );
    }

    // change is_seen to true to all record and returns the new count value 
    public function resetNotificationCount(Request $request)
    {
        try {
            $query = Notification::where('notifiable_id', $request->userId)
                ->orderBy('created_at', 'desc')->limit(20);
            // udpate the is_seen to true
            $result = DB::transaction(function () use ($query) {
                if (!$query->update(['is_seen' => true])) {
                    return false;
                }
                return true;
            });
            if (!$result) {
                $this->sendError(statusMessage: 'Error reseting notifications');
            }
            $unseenNotifications = $query->where('is_seen', false)->count();
            return $this->sendSuccess(statusMessage: 'Notification is_seen value successfully reset.', attribute: 'unseenNotifications', items: $unseenNotifications);
        } catch (\Exception $e) {
            return $this->sendError(statusMessage: 'Error resetting notifications with exception', exceptionMessage: $e->getMessage());
        }
    }

    public function markNotificationAsRead(Request $reqeust)
    {
        $notification = Notification::with([
            'post' => function ($query) {
                $query->select('id', 'slug');
            }
        ])->find($reqeust->notificationId);

        if (!$notification) {
            return $this->sendError(statusMessage: 'Notification record not found');
        }

        if (!$notification->update(['is_read' => true])) {
            return $this->sendError(statusMessage: 'Error reading notification');
        }
        return $this->sendSuccess(statusMessage: 'Notification successfully read', attribute: 'notification', items: $notification);
    }


    public function viewNotification() {}

    // remove notification icon fucntion 
    // user table should have a notification count field, in which notifications should be added and once clicked, it should be reverted back to 0
}
