<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewJobNotification extends Notification
{
    use Queueable;

    public $job;

    public function __construct($job)
    {
        $this->job = $job;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Job Opportunity!',
            'message' => $this->job->company . ' is hiring for: ' . $this->job->title,
            'url' => route('jobs.index'),
            'icon' => 'briefcase',
        ];
    }
}
