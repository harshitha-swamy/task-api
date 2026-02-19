<?php
namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTaskAssignedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public Task $task) {}

    public function handle(): void
    {
        $assignedUser = $this->task->assignedUser;

        if (!$assignedUser) return;

        // Simulate email notification
        Log::info("📧 Email sent to {$assignedUser->email} for task: {$this->task->title}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("❌ Failed to send notification for task ID {$this->task->id}: {$exception->getMessage()}");
    }
}