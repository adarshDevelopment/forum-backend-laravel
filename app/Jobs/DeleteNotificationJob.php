<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DeleteNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $interactorUserId, public int $postId)
    {
        $this->interactorUserId = $interactorUserId;
        $this->postId = $postId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Notification::where('interactor_user_id', $this->interactorUserId)
                ->where('post_id', $this->postId)
                ->delete();
        } catch (\Exception $e) {
            Log::error('Error fetching notification. ' . $e->getMessage());
        }
    }
}
