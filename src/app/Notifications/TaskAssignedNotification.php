<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Task $task,
        protected Project $project
    )
    {
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
         return (new MailMessage)
            ->subject("Task assigned to you: {$this->task->title}")
            ->view('emails.task-assigned', [
                'user' => $notifiable,
                'task' => $this->task,
                'project' => $this->project,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'project_id' => $this->project->id,
            'title' => $this->task->title,
            'message' => "You have been assigned the task '{$this->task->title}'.",
        ];
    }
}
