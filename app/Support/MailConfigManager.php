<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class MailConfigManager
{
    /**
     * Apply admin-configured SMTP settings from the settings table at runtime.
     */
    public static function applyRuntimeMailConfig(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            $mailer = trim((string) Setting::getVal('mail_mailer', ''));
            $host = trim((string) Setting::getVal('mail_host', ''));
            $port = trim((string) Setting::getVal('mail_port', ''));
            $username = trim((string) Setting::getVal('mail_username', ''));
            $password = (string) Setting::getVal('mail_password', '');
            $encryption = trim((string) Setting::getVal('mail_encryption', ''));
            $fromAddress = trim((string) Setting::getVal('mail_from_address', ''));
            $fromName = trim((string) Setting::getVal('mail_from_name', ''));

            if ($mailer !== '') {
                Config::set('mail.default', $mailer);
            }
            if ($host !== '') {
                Config::set('mail.mailers.smtp.host', $host);
            }
            if ($port !== '' && is_numeric($port)) {
                Config::set('mail.mailers.smtp.port', (int) $port);
            }
            if ($username !== '') {
                Config::set('mail.mailers.smtp.username', $username);
            }
            if ($password !== '') {
                Config::set('mail.mailers.smtp.password', $password);
            }
            if ($encryption !== '') {
                Config::set('mail.mailers.smtp.scheme', $encryption === 'ssl' ? 'smtps' : ($encryption === 'none' ? null : 'smtp'));
            }
            if ($fromAddress !== '' && filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
                Config::set('mail.from.address', $fromAddress);
            }
            if ($fromName !== '') {
                Config::set('mail.from.name', $fromName);
            }
        } catch (\Throwable $e) {
            // Never disrupt app boot if DB is unavailable
        }
    }

    /**
     * Determine whether an email channel can be safely attempted for a user.
     */
    public static function canSendEmailTo($notifiable): bool
    {
        $email = trim((string) ($notifiable->email ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mailer = (string) config('mail.default', 'log');
        return $mailer !== '';
    }
}
