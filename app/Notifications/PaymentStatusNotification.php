<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentStatusNotification extends Notification
{
    use Queueable;

    public Payment $payment;
    public string $status;

    public function __construct(Payment $payment, ?string $status = null)
    {
        $this->payment = $payment;
        $this->status = $status ?? (string) $payment->status;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $course = $this->payment->course;
        $courseTitle = $course?->title ?? 'your course';
        $trx = $this->payment->transaction_id ?: ('#' . $this->payment->id);

        return match ($this->status) {
            'verified' => [
                'title' => '🎉 Congratulations! Payment Verified',
                'message' => "Congratulations! Your payment for \"{$courseTitle}\" (TrxID: {$trx}) has succeeded and your enrollment is confirmed. Start learning now!",
                'status' => 'verified',
                'status_label' => 'Succeeded',
                'url' => $course ? route('courses.show', $course) : route('dashboard'),
                'icon' => 'check-circle',
                'payment_id' => $this->payment->id,
                'course_id' => $this->payment->course_id,
            ],
            'processing' => [
                'title' => 'Payment Processing',
                'message' => "Your payment for \"{$courseTitle}\" (TrxID: {$trx}) is currently processing. We will notify you as soon as verification completes.",
                'status' => 'processing',
                'status_label' => 'Processing',
                'url' => route('dashboard'),
                'icon' => 'clock',
                'payment_id' => $this->payment->id,
                'course_id' => $this->payment->course_id,
            ],
            'rejected' => [
                'title' => 'Payment Not Verified',
                'message' => "Your payment for \"{$courseTitle}\" (TrxID: {$trx}) could not be verified. Please re-check your Transaction ID or contact support.",
                'status' => 'rejected',
                'status_label' => 'Rejected',
                'url' => $course ? route('courses.checkout', $course) : route('dashboard'),
                'icon' => 'times-circle',
                'payment_id' => $this->payment->id,
                'course_id' => $this->payment->course_id,
            ],
            default => [
                'title' => 'Payment Received (Pending)',
                'message' => "We received your payment request for \"{$courseTitle}\" (TrxID: {$trx}). It is queued for verification.",
                'status' => 'pending',
                'status_label' => 'Pending',
                'url' => route('dashboard'),
                'icon' => 'clock',
                'payment_id' => $this->payment->id,
                'course_id' => $this->payment->course_id,
            ],
        };
    }
}
