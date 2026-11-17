<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Models\Setting;
use App\Models\User;
use App\Support\MailConfigManager;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification
{
    use Queueable;

    public Certificate $certificate;
    public bool $sendMail;

    public function __construct(Certificate $certificate, bool $sendMail = true)
    {
        $this->certificate = $certificate->loadMissing('course');
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
        $course = $this->certificate->course;
        $courseTitle = $course?->title ?? 'Executive Program';
        $siteName = Setting::getVal('site_name', 'ACS Academy');
        $certUrl = $course ? route('courses.certificate', $course) : route('dashboard');
        $verifyUrl = route('certificate.verify', $this->certificate->certificate_number);

        return (new MailMessage)
            ->subject("Official Certificate Issued — {$courseTitle}")
            ->greeting("Congratulations, {$notifiable->name}!")
            ->line("Your official digital certificate for \"{$courseTitle}\" has been issued by {$siteName}.")
            ->line("Credential ID: {$this->certificate->certificate_number}")
            ->action('View & Download Certificate', $certUrl)
            ->line("Public Verification Link: {$verifyUrl}");
    }

    public function toArray($notifiable): array
    {
        $course = $this->certificate->course;
        $courseTitle = $course?->title ?? 'Executive Program';

        return [
            'title' => '🎓 Official Certificate Issued!',
            'message' => "Congratulations! Your certificate ({$this->certificate->certificate_number}) for \"{$courseTitle}\" is ready to view & download.",
            'url' => $course ? route('courses.certificate', $course) : route('dashboard'),
            'verify_url' => route('certificate.verify', $this->certificate->certificate_number),
            'certificate_number' => $this->certificate->certificate_number,
            'course_id' => $this->certificate->course_id,
            'icon' => 'award',
        ];
    }

    /**
     * Dispatch certificate notification safely (falls back to in-app database-only if mail fails).
     */
    public static function sendSafely(User $student, Certificate $certificate, bool $includeEmail = true): array
    {
        MailConfigManager::applyRuntimeMailConfig();

        $emailSent = false;
        if ($includeEmail && MailConfigManager::canSendEmailTo($student)) {
            try {
                $student->notify(new self($certificate, true));
                return ['database' => true, 'mail' => true];
            } catch (\Throwable $e) {
                // Fall back to database-only so in-app notification is never lost on SMTP error
            }
        }

        $student->notify(new self($certificate, false));
        return ['database' => true, 'mail' => $emailSent];
    }
}
