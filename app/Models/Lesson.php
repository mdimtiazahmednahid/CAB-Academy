<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Detect the video or live classroom provider from video_url.
     */
    public function getVideoProviderAttribute(): ?string
    {
        $url = trim((string) ($this->video_url ?? ''));
        if ($url === '') {
            return null;
        }

        $lower = strtolower($url);

        if (str_contains($lower, 'meet.jit.si') || str_contains($lower, '8x8.vc') || str_contains($lower, 'jitsi')) {
            return 'jitsi';
        }
        if (str_contains($lower, 'zoom.us')) {
            return 'zoom';
        }
        if (str_contains($lower, 'meet.google.com')) {
            return 'google_meet';
        }
        if (str_contains($lower, 'youtube.com') || str_contains($lower, 'youtu.be')) {
            return 'youtube';
        }
        if (str_contains($lower, 'vimeo.com')) {
            return 'vimeo';
        }

        return 'custom';
    }

    /**
     * Determine whether this lesson is an interactive live class (Jitsi, Zoom, or Google Meet).
     */
    public function getIsLiveClassAttribute(): bool
    {
        return in_array($this->video_provider, ['jitsi', 'zoom', 'google_meet'], true);
    }

    /**
     * Human-friendly label for curriculum lists & badges.
     */
    public function getProviderLabelAttribute(): string
    {
        return match ($this->video_provider) {
            'jitsi' => 'Jitsi Meet Live',
            'zoom' => 'Zoom Live Class',
            'google_meet' => 'Google Meet Live',
            'youtube', 'vimeo', 'custom' => 'Video Lesson',
            default => 'Reading Note',
        };
    }

    /**
     * Resolve full classroom stage configuration for in-browser embedding or pop-out launch.
     */
    public function resolveClassroomStage(?User $user = null): array
    {
        $rawUrl = trim((string) ($this->video_url ?? ''));
        $provider = $this->video_provider;
        $userName = $user?->name ?: 'ACS Student';
        $userEmail = $user?->email ?: '';

        $stage = [
            'provider' => $provider,
            'provider_label' => $this->provider_label,
            'is_live' => $this->is_live_class,
            'can_iframe' => false,
            'embed_url' => null,
            'external_url' => $rawUrl !== '' ? $rawUrl : null,
            'meeting_id' => null,
        ];

        if ($rawUrl === '') {
            return $stage;
        }

        if ($provider === 'jitsi') {
            $baseUrl = explode('#', $rawUrl)[0];
            $path = trim((string) parse_url($baseUrl, PHP_URL_PATH), '/');
            $stage['meeting_id'] = $path !== '' ? $path : 'ACS-Live-Classroom';

            $hashParams = [
                'config.prejoinPageEnabled=false',
                'config.startWithAudioMuted=true',
                'config.disableDeepLinking=true',
                'userInfo.displayName=' . rawurlencode('"' . $userName . '"'),
            ];
            if ($userEmail !== '') {
                $hashParams[] = 'userInfo.email=' . rawurlencode('"' . $userEmail . '"');
            }

            $stage['can_iframe'] = true;
            $stage['embed_url'] = $baseUrl . '#' . implode('&', $hashParams);
            $stage['external_url'] = $baseUrl;
            return $stage;
        }

        if ($provider === 'zoom') {
            $meetingId = null;
            if (preg_match('/(?:\/j\/|\/wc\/|\/s\/)(\d{8,14})/', $rawUrl, $matches)) {
                $meetingId = $matches[1];
            } elseif (preg_match('/(\d{9,12})/', $rawUrl, $matches)) {
                $meetingId = $matches[1];
            }

            $query = [];
            $queryString = (string) parse_url($rawUrl, PHP_URL_QUERY);
            if ($queryString !== '') {
                parse_str($queryString, $query);
            }
            $pwd = $query['pwd'] ?? null;

            $stage['meeting_id'] = $meetingId;
            $stage['can_iframe'] = true;

            if ($meetingId) {
                $embedUrl = 'https://app.zoom.us/wc/' . $meetingId . '/join?prefer=1&un=' . rawurlencode(base64_encode($userName));
                if ($pwd) {
                    $embedUrl .= '&pwd=' . rawurlencode((string) $pwd);
                }
                $stage['embed_url'] = $embedUrl;
            } else {
                $stage['embed_url'] = $rawUrl;
            }

            return $stage;
        }

        if ($provider === 'google_meet') {
            if (preg_match('/([a-z]{3}-[a-z]{4}-[a-z]{3})/i', $rawUrl, $matches)) {
                $stage['meeting_id'] = strtolower($matches[1]);
            }
            // Google Meet enforces X-Frame-Options: SAMEORIGIN; use dedicated pop-out stage launcher
            $stage['can_iframe'] = false;
            $stage['embed_url'] = null;
            return $stage;
        }

        if ($provider === 'youtube') {
            $embedUrl = $rawUrl;
            if (preg_match('/(?:youtube\.com\/(?:watch\?v=|live\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $rawUrl, $matches)) {
                $embedUrl = 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0';
                $stage['meeting_id'] = $matches[1];
            }
            $stage['can_iframe'] = true;
            $stage['embed_url'] = $embedUrl;
            return $stage;
        }

        if ($provider === 'vimeo') {
            $embedUrl = $rawUrl;
            if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $rawUrl, $matches)) {
                $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
                $stage['meeting_id'] = $matches[1];
            }
            $stage['can_iframe'] = true;
            $stage['embed_url'] = $embedUrl;
            return $stage;
        }

        $stage['can_iframe'] = true;
        $stage['embed_url'] = $rawUrl;
        return $stage;
    }
}
