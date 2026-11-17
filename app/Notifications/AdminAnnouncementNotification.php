<?php

namespace App\Notifications;

use App\Support\MailConfigManager;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminAnnouncementNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $url;
    public bool $sendMail;

    public function __construct($title, $message, $url = null, bool $sendMail = false)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->sendMail = $sendMail;
    }

    public function via($notifiable): array
    {
        if ($this->sendMail && MailConfigManager::canSendEmailTo($notifiable)) {
            return ['database', 'mail'];
        }

        return ['database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message);

        if (!empty($this->url) && $this->url !== '#') {
            $mail->action('View Details', $this->url);
        }

        return $mail;
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => '📢 ' . $this->title,
            'message' => $this->message,
            'url' => $this->url ?? '#',
            'icon' => 'speakerphone',
        ];
    }
}
