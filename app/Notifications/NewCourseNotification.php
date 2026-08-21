<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCourseNotification extends Notification
{
    use Queueable;

    public $course;

    public function __construct($course)
    {
        $this->course = $course;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Course Published!',
            'message' => 'Check out our new course: ' . $this->course->title,
            'url' => route('courses.show', $this->course->id),
            'icon' => 'academic-cap', // Icon identifier for frontend
        ];
    }
}
