<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'thumbnail',
        'is_published',
        'cover_image',
        'duration',
        'start_date',
        'venue',
        'batch',
        'show_batch',
        'target_audience',
        'price',
        'level',
        'category',
        'instructor_id',
        'certificate_enabled',
        'enrollment_fields',
        'counselor_phone',
        'whatsapp_message',
        'views'
    ];

    protected $casts = [
        'start_date' => 'date',
        'price' => 'decimal:2',
        'is_published' => 'boolean',
        'certificate_enabled' => 'boolean',
        'show_batch' => 'boolean',
        'enrollment_fields' => 'array',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function setThumbnailAttribute($value)
    {
        $this->attributes['thumbnail'] = $value;
        $this->attributes['cover_image'] = $value;
    }

    public function setCoverImageAttribute($value)
    {
        $this->attributes['cover_image'] = $value;
        $this->attributes['thumbnail'] = $value;
    }

    public function getThumbnailAttribute($value)
    {
        return $value ?: ($this->attributes['cover_image'] ?? null);
    }

    public function getCoverImageAttribute($value)
    {
        return $value ?: ($this->attributes['thumbnail'] ?? null);
    }

    protected static function booted(): void
    {
        static::saving(function ($course) {
            // Guarantee single source of truth between cover_image and thumbnail
            $img = $course->cover_image ?: $course->thumbnail;
            if (!empty($img)) {
                $course->attributes['cover_image'] = $img;
                $course->attributes['thumbnail'] = $img;
            }

            // Ensure instructor_id points to users.id only if instructor_id is not already a valid users.id
            if (!empty($course->instructor_id)) {
                $userExists = User::where('id', $course->instructor_id)->exists();
                if (!$userExists) {
                    $inst = Instructor::find($course->instructor_id);
                    if ($inst && $inst->user_id) {
                        $course->instructor_id = $inst->user_id;
                    }
                }
            }
        });

        static::saved(function ($course) {
            try {
                \Illuminate\Support\Facades\Cache::forget('fb_link_preview_v1_' . md5($course->short_url));
                \Illuminate\Support\Facades\Cache::forget('fb_link_preview_v1_' . md5(url('/courses/' . $course->id)));
                if ($course->is_published && !$course->wasChanged('views') && app()->environment('production')) {
                    \App\Support\SeoIndexingManager::pingSearchEngines([
                        url('/courses/' . $course->id),
                        url('/catalog'),
                    ]);
                }
            } catch (\Throwable $e) {
                // Ignore cache/indexing errors
            }
        });
    }

    /**
     * Get fully-qualified cover image URL with reliable storage prefixing and fallback.
     */
    public function getCoverImageUrlAttribute(): string
    {
        $img = $this->cover_image ?: $this->thumbnail;
        $default = 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=80';
        if (!$img) {
            return $default;
        }
        return storage_asset($img, $default);
    }

    /**
     * Alias for cover_image_url ensuring 100% parity across all views.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->cover_image_url;
    }

    /**
     * Concise, social-optimized short link for this course (e.g. https://acsacademy.bd/c/5).
     */
    public function getShortUrlAttribute(): string
    {
        return url('/c/' . $this->id);
    }

    /**
     * Pre-formatted one-click Facebook Sharer dialog URL.
     */
    public function getFacebookShareUrlAttribute(): string
    {
        return 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($this->short_url);
    }

    /**
     * Direct Facebook Sharing Debugger URL to refresh Open Graph scrape cache on demand.
     */
    public function getFacebookDebuggerUrlAttribute(): string
    {
        return 'https://developers.facebook.com/tools/debug/?q=' . urlencode($this->short_url);
    }

    /**
     * Pre-formatted WhatsApp share link with stylish bold serif headline and short URL.
     */
    public function getWhatsappShareUrlAttribute(): string
    {
        $text = $this->stylish_post_header . "\n\n" . $this->short_url;
        return 'https://api.whatsapp.com/send?text=' . urlencode($text);
    }

    /**
     * Auto-detected dynamic start date / batch / duration hook for Facebook & social link preview cards.
     * Example: "Starting 30 October"
     */
    public function getSocialStartSuffixAttribute(): string
    {
        if ($this->start_date) {
            return 'Starting ' . $this->start_date->format('d F');
        }

        if (!empty($this->batch) && $this->show_batch) {
            return trim($this->batch) . ' Admission Open';
        }

        if (!empty($this->duration)) {
            return trim($this->duration) . ' Live Program';
        }

        return 'Registration Open';
    }

    /**
     * Plain-text combined social card headline: "{Course Title}. {Start Suffix}"
     * Example: "PGD in Supply Chain Management. Starting 30 October"
     */
    public function getSocialCardHeadlineAttribute(): string
    {
        $cleanTitle = rtrim(trim((string) $this->title), '. ');
        $suffix = trim($this->social_start_suffix);

        return $suffix !== '' ? "{$cleanTitle}. {$suffix}" : $cleanTitle;
    }

    /**
     * Unicode Mathematical Bold Serif headline for Open Graph (og:title) and Facebook Link Cards.
     * Example: "𝐏𝐆𝐃 𝐢𝐧 𝐒𝐮𝐩𝐩𝐥𝐲 𝐂𝐡𝐚𝐢𝐧 𝐌𝐚𝐧𝐚𝐠𝐞𝐦𝐞𝐧𝐭. 𝐒𝐭𝐚𝐫𝐭𝐢𝐧𝐠 𝟑𝟎 𝐎𝐜𝐭𝐨𝐛𝐞𝐫"
     */
    public function getStylishOgTitleAttribute(): string
    {
        return \App\Support\StylishTextFormatter::toBoldSerif($this->social_card_headline);
    }

    /**
     * Uppercase Unicode Mathematical Bold Serif post caption header.
     * Example: "𝐏𝐆𝐃 𝐈𝐍 𝐒𝐔𝐏𝐏𝐋𝐘 𝐂𝐇𝐀𝐈𝐍 𝐌𝐀𝐍𝐀𝐆𝐄𝐌𝐄𝐍𝐓"
     */
    public function getStylishPostHeaderAttribute(): string
    {
        $cleanTitle = rtrim(trim((string) $this->title), '. ');
        return \App\Support\StylishTextFormatter::toUpperBoldSerif($cleanTitle);
    }

    /**
     * Clean, minimalist Facebook/Social post caption matching executive EdTech feed standards:
     * Single-line uppercase Mathematical Bold Serif headline + short link (no emoji clutter).
     */
    public function getStylishSocialCaptionAttribute(): string
    {
        return $this->stylish_post_header . "\n\n" . $this->short_url;
    }

    /**
     * High-resolution, fully-qualified Open Graph banner image URL (1200x630 optimal for Facebook/Twitter/LinkedIn cards).
     */
    public function getOgImageUrlAttribute(): string
    {
        $img = $this->cover_image ?: $this->thumbnail;
        $default = 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1200&h=630&q=85';
        if (!$img) {
            return $default;
        }

        if (str_starts_with($img, 'data:image/')) {
            return url('/courses/' . $this->id . '/og-image.jpg');
        }

        $url = storage_asset($img, $default);

        // Ensure 100% absolute URL for social crawlers (Facebook, WhatsApp, LinkedIn, Twitter)
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = url($url);
        }

        // If Unsplash, ensure 1200x630 resolution for pristine social cards
        if (str_contains($url, 'images.unsplash.com')) {
            $url = preg_replace('/w=\d+/', 'w=1200', $url);
            if (!str_contains($url, 'h=')) {
                $url .= '&h=630';
            } else {
                $url = preg_replace('/h=\d+/', 'h=630', $url);
            }
        }

        return $url;
    }

    /**
     * Dynamic Counselor / Hotline phone number (per-course override -> global settings -> fallback).
     */
    public function getEffectiveCounselorPhoneAttribute(): string
    {
        $custom = trim((string) ($this->counselor_phone ?? ''));
        if ($custom !== '') {
            return $custom;
        }

        $hotline = trim((string) Setting::getVal('contact_hotline', ''));
        if ($hotline !== '') {
            return $hotline;
        }

        $whatsapp = trim((string) Setting::getVal('contact_whatsapp', ''));
        if ($whatsapp !== '') {
            return $whatsapp;
        }

        $sitePhone = trim((string) Setting::getVal('site_phone', ''));
        if ($sitePhone !== '') {
            return $sitePhone;
        }

        return '01917-054664';
    }

    /**
     * Clean tel: URI target for direct phone calls on this course.
     */
    public function getEffectiveCounselorTelAttribute(): string
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $this->effective_counselor_phone);
        return $cleaned ?: '01917054664';
    }

    /**
     * Normalized WhatsApp digits (with country code 88 for BD 01... numbers) for wa.me links.
     */
    public function getEffectiveWhatsappNumberAttribute(): string
    {
        $raw = trim((string) ($this->counselor_phone ?? ''));
        if ($raw === '') {
            $raw = trim((string) Setting::getVal('contact_whatsapp', ''));
        }
        if ($raw === '') {
            $raw = trim((string) Setting::getVal('landing_counseling_wa_number', ''));
        }
        if ($raw === '') {
            $raw = trim((string) Setting::getVal('contact_hotline', ''));
        }
        if ($raw === '') {
            $raw = trim((string) Setting::getVal('site_phone', ''));
        }

        $digits = preg_replace('/[^0-9]/', '', $raw);
        if (!$digits) {
            return '8801917054664';
        }

        // Normalize 11-digit Bangladesh mobile numbers (01XXXXXXXXX -> 8801XXXXXXXXX)
        if (strlen($digits) === 11 && str_starts_with($digits, '01')) {
            return '88' . $digits;
        }

        return $digits;
    }

    /**
     * Dynamic WhatsApp inquiry message (no hardcoded brand name).
     */
    public function getEffectiveWhatsappMessageAttribute(): string
    {
        $formattedFee = '৳' . number_format((float) $this->price, 0);
        $template = trim((string) ($this->whatsapp_message ?? ''));
        if ($template === '') {
            $template = trim((string) Setting::getVal('whatsapp_inquiry_template', ''));
        }

        if ($template !== '') {
            $msg = str_replace(
                ['{title}', '{price}', '{fee}', '{batch}', '{url}'],
                [$this->title, $formattedFee, $formattedFee, (string) ($this->batch ?? ''), $this->short_url],
                $template
            );
            // Strip any legacy hardcoded "CAB Academy" greeting if present
            $msg = preg_replace('/Assalamu Alaikum\s+(?:CAB|ACS)\s+Academy!/i', 'Assalamu Alaikum!', $msg);
            if (!str_contains($msg, $this->short_url)) {
                $msg = rtrim($msg) . ' ' . $this->short_url;
            }
            return $msg;
        }

        return "Assalamu Alaikum! I would like to inquire/enroll in *{$this->title}* (Fee: {$formattedFee}). Please share batch details and enrollment procedure: " . $this->short_url;
    }

    /**
     * Pre-formatted WhatsApp Academic Counselor direct message URL.
     */
    public function getCounselorWhatsappUrlAttribute(): string
    {
        return 'https://wa.me/' . $this->effective_whatsapp_number . '?text=' . urlencode($this->effective_whatsapp_message);
    }

    /**
     * Pre-formatted LinkedIn share dialog URL.
     */
    public function getLinkedinShareUrlAttribute(): string
    {
        return 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($this->short_url);
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->price > 0 ? '৳' . number_format((float) $this->price, 0) : 'Free';
    }

    public function getBatchNoAttribute(): ?string
    {
        return $this->batch;
    }

    /**
     * Resolve course attendance mode from venue ('online', 'in_person', or 'hybrid').
     */
    public function getAttendanceModeAttribute(): string
    {
        $venue = strtolower(trim((string) ($this->venue ?? '')));
        if ($venue === '') {
            return 'online';
        }

        if (str_contains($venue, 'hybrid') || str_contains($venue, 'both')) {
            return 'hybrid';
        }

        $hasOnline = (bool) preg_match('/(online|zoom|virtual|webinar|meet)/i', $venue);
        $hasCampus = (bool) preg_match('/(campus|in-person|in\s+person|offline|classroom|training\s+room|mirpur|pallabi|banani|uttara|physical)/i', $venue);

        if ($hasOnline && $hasCampus) {
            return 'hybrid';
        }

        if ($hasCampus && !$hasOnline) {
            return 'in_person';
        }

        return 'online';
    }

    public function isOnlineOnly(): bool
    {
        return $this->attendance_mode === 'online';
    }

    public function isCampusOnly(): bool
    {
        return $this->attendance_mode === 'in_person';
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function instructorProfile()
    {
        return $this->hasOne(Instructor::class, 'user_id', 'instructor_id');
    }

    /**
     * Bulletproof Lead Faculty resolver handling User ID, Instructor ID, and customized Faculty sync across all pages.
     */
    public function getLeadFacultyAttribute()
    {
        $directProfile = null;

        if ($this->instructor_id) {
            $directProfile = Instructor::where('user_id', $this->instructor_id)
                ->where('is_active', true)
                ->first();

            if ($directProfile && $directProfile->isCustomized()) {
                return $directProfile;
            }
        }

        // Check if any active Instructor profile has been customized by the admin (custom photo or custom name)
        $activeInstructors = Instructor::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderByDesc('updated_at')
            ->get();

        $customizedInstructor = $activeInstructors->first(fn ($i) => $i->isCustomized());
        if ($customizedInstructor) {
            return $customizedInstructor;
        }

        if ($directProfile) {
            return $directProfile;
        }

        if ($this->instructor_id) {
            if ($byInstId = Instructor::where('id', $this->instructor_id)->where('is_active', true)->first()) {
                return $byInstId;
            }
            if ($this->instructor && (!empty($this->instructor->profile_picture) || $this->instructor->role === 'instructor')) {
                return $this->instructor;
            }
        }

        if ($activeInstructors->isNotEmpty()) {
            return $activeInstructors->first();
        }

        return $this->instructor;
    }

    public function getInstructorNameAttribute(): string
    {
        $lead = $this->lead_faculty;
        return $lead ? $lead->name : (Setting::getVal('site_name', 'ACS Academy') . ' Senior Faculty');
    }

    public function getInstructorTitleAttribute(): string
    {
        $lead = $this->lead_faculty;
        if ($lead && !empty($lead->title)) {
            return $lead->title;
        }
        return 'Executive Trade & Customs Specialist';
    }

    public function getInstructorAvatarUrlAttribute(): string
    {
        $lead = $this->lead_faculty;
        if ($lead) {
            if (!empty($lead->avatar_url)) {
                return $lead->avatar_url;
            }
            if (!empty($lead->profile_picture_url)) {
                return $lead->profile_picture_url;
            }
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->instructor_name) . '&background=051c45&color=fff';
    }

    public function getInstructorBioAttribute(): string
    {
        $lead = $this->lead_faculty;
        if ($lead && !empty($lead->bio)) {
            return $lead->bio;
        }
        return 'Distinguished Faculty Specialist in international trade, corporate customs, and regulatory compliance.';
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user')->withTimestamps();
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Determine if a certificate is available/accessible for the given user.
     * Certificates are only accessible if explicitly released by admin or individually issued.
     */
    public function isCertificateAvailableFor(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Admin can always preview/view certificates
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }

        // Check if an individual certificate was already generated/issued by admin
        if ($this->certificates()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Course-wide certificate release: requires certificate_enabled = true AND active enrollment/payment
        if ($this->certificate_enabled) {
            $isEnrolled = $user->enrolledCourses()->where('course_id', $this->id)->exists();
            $isVerifiedPaid = $user->payments()->where('course_id', $this->id)->where('status', 'verified')->exists();
            return $isEnrolled || $isVerifiedPaid;
        }

        return false;
    }

    /**
     * Built-in rapid enrollment form templates.
     */
    public static function getBuiltinTemplates(): array
    {
        return [
            'standard' => [
                'key' => 'standard',
                'name' => 'Standard Professional',
                'icon' => 'fa-user-check',
                'badge' => 'Default',
                'description' => 'Name, Phone, and Required Email. Optimal for LMS access, notifications, and verifiable digital certificates.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '017xxxxxxxx'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'name@company.com'],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                ],
            ],
            'quick_lead' => [
                'key' => 'quick_lead',
                'name' => 'Quick Lead (No Email Required)',
                'icon' => 'fa-bolt',
                'badge' => 'Highest Velocity',
                'description' => 'Fastest 15-sec conversion: Name + Phone only. Email is disabled. Ideal for free workshops & social ad traffic.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '017xxxxxxxx'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => false, 'is_enabled' => false, 'is_system' => true, 'placeholder' => 'name@company.com'],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                ],
            ],
            'corporate' => [
                'key' => 'corporate',
                'name' => 'Corporate & Executive',
                'icon' => 'fa-building',
                'badge' => 'B2B & Executive',
                'description' => 'Name, Phone, Email, Organization / Company, and Designation for corporate cohorts and executive billing.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '017xxxxxxxx'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'name@company.com'],
                    ['name' => 'organization', 'label' => 'Organization / Company', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Apex Footwear Ltd.'],
                    ['name' => 'designation', 'label' => 'Designation / Job Title', 'type' => 'text', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Senior Commercial Manager'],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                ],
            ],
            'comprehensive' => [
                'key' => 'comprehensive',
                'name' => 'Comprehensive Application',
                'icon' => 'fa-clipboard-list',
                'badge' => 'Full Dossier',
                'description' => 'In-depth executive profile: Name, Phone, Email, Company, Role, Experience Level, City, and Expectations.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '017xxxxxxxx'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'name@company.com'],
                    ['name' => 'organization', 'label' => 'Organization / Company', 'type' => 'text', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Beximco Group'],
                    ['name' => 'designation', 'label' => 'Designation / Role', 'type' => 'text', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Supply Chain Officer'],
                    ['name' => 'experience_level', 'label' => 'Experience Level', 'type' => 'select', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'Select your experience level', 'options' => ['Fresh Graduate / Entry Level', '1–3 Years Experience', '4–7 Years Experience', '8+ Years (Senior / Leadership)']],
                    ['name' => 'city', 'label' => 'City / District', 'type' => 'text', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Dhaka / Chattogram'],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                    ['name' => 'comments', 'label' => 'Expectations / Queries', 'type' => 'textarea', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'Any specific topics or case studies you want covered?'],
                ],
            ],
            'online_webinar' => [
                'key' => 'online_webinar',
                'name' => 'Online Masterclass / Webinar',
                'icon' => 'fa-video',
                'badge' => 'Live Zoom',
                'description' => 'Tailored for Live Zoom workshops: Name, WhatsApp Mobile, Optional Email, and 1-Tap Profession Choice Pills.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'WhatsApp / Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '017xxxxxxxx'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => false, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'name@company.com (for certificate)'],
                    ['name' => 'profession', 'label' => 'Current Profile', 'type' => 'radio', 'is_required' => true, 'is_enabled' => true, 'is_system' => false, 'placeholder' => '', 'options' => ['Corporate Professional', 'Business Owner', 'Job Seeker / Graduate', 'University Student']],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                ],
            ],
            'academic_cohort' => [
                'key' => 'academic_cohort',
                'name' => 'University & Career Bootcamp',
                'icon' => 'fa-graduation-cap',
                'badge' => 'Academic / Youth',
                'description' => 'Designed for career-readiness programs: Name, Phone, Email, University / Institution, and Academic Status.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '017xxxxxxxx'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'student@university.edu'],
                    ['name' => 'institution', 'label' => 'University / Institution Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. University of Dhaka / NSU / BRACU'],
                    ['name' => 'academic_status', 'label' => 'Current Academic / Career Stage', 'type' => 'radio', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => '', 'options' => ['Final Year Student', 'Recent Graduate', 'Early Career Executive']],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                ],
            ],
        ];
    }

    /**
     * Curated 1-click smart question blocks that Admin can add to any course registration form with one click.
     */
    public static function getQuickAddQuestions(): array
    {
        return [
            [
                'name' => 'organization',
                'label' => 'Organization / Company',
                'icon' => 'fa-building',
                'type' => 'text',
                'is_required' => true,
                'placeholder' => 'e.g. Square Pharmaceuticals PLC',
                'options' => null,
            ],
            [
                'name' => 'designation',
                'label' => 'Designation / Job Title',
                'icon' => 'fa-briefcase',
                'type' => 'text',
                'is_required' => false,
                'placeholder' => 'e.g. Senior Commercial Manager',
                'options' => null,
            ],
            [
                'name' => 'experience_level',
                'label' => 'Experience Level',
                'icon' => 'fa-chart-line',
                'type' => 'select',
                'is_required' => false,
                'placeholder' => 'Select your experience level',
                'options' => ['Fresh Graduate / Entry Level', '1–3 Years Experience', '4–7 Years Experience', '8+ Years (Senior / Leadership)'],
            ],
            [
                'name' => 'profession',
                'label' => 'Current Profile / Role',
                'icon' => 'fa-user-tie',
                'type' => 'radio',
                'is_required' => true,
                'placeholder' => '',
                'options' => ['Corporate Professional', 'Business Owner', 'Job Seeker / Graduate', 'University Student'],
            ],
            [
                'name' => 'industry_sector',
                'label' => 'Industry / Sector',
                'icon' => 'fa-industry',
                'type' => 'select',
                'is_required' => false,
                'placeholder' => 'Select your industry sector',
                'options' => ['RMG & Textiles', 'Banking & Financial Services', 'Supply Chain & Freight', 'Manufacturing & FMCG', 'IT & Technology', 'Education / Other'],
            ],
            [
                'name' => 'city',
                'label' => 'City / District',
                'icon' => 'fa-map-marker-alt',
                'type' => 'text',
                'is_required' => false,
                'placeholder' => 'e.g. Dhaka / Chattogram',
                'options' => null,
            ],
            [
                'name' => 'institution',
                'label' => 'University / Highest Degree',
                'icon' => 'fa-university',
                'type' => 'text',
                'is_required' => false,
                'placeholder' => 'e.g. MBA, University of Dhaka',
                'options' => null,
            ],
            [
                'name' => 'linkedin_url',
                'label' => 'LinkedIn Profile URL',
                'icon' => 'fa-link',
                'type' => 'url',
                'is_required' => false,
                'placeholder' => 'https://linkedin.com/in/yourprofile',
                'options' => null,
            ],
            [
                'name' => 'nid_number',
                'label' => 'National ID (NID) / Passport',
                'icon' => 'fa-id-card',
                'type' => 'text',
                'is_required' => false,
                'placeholder' => 'For official certificate registry',
                'options' => null,
            ],
            [
                'name' => 'referral_source',
                'label' => 'How Did You Hear About Us?',
                'icon' => 'fa-bullhorn',
                'type' => 'select',
                'is_required' => false,
                'placeholder' => 'Select source',
                'options' => ['Facebook', 'LinkedIn', 'WhatsApp Group', 'Colleague / Friend Referral', 'Website / Search'],
            ],
            [
                'name' => 'comments',
                'label' => 'Expectations / Key Questions',
                'icon' => 'fa-comment-dots',
                'type' => 'textarea',
                'is_required' => false,
                'placeholder' => 'Any specific topics or challenges you want covered in this program?',
                'options' => null,
            ],
        ];
    }

    /**
     * Get effective registration/enrollment fields for this course.
     * Guaranteed array of fields with name, label, type, is_required, is_enabled, placeholder, options.
     */
    public function getEffectiveEnrollmentFieldsAttribute(): array
    {
        $fields = $this->enrollment_fields;
        if (is_array($fields) && !empty($fields)) {
            return $fields;
        }

        // Fallback to Standard Professional template
        return self::getBuiltinTemplates()['standard']['fields'];
    }

    /**
     * Alias for effective_enrollment_fields.
     */
    public function getEffectiveRegistrationFieldsAttribute(): array
    {
        return $this->effective_enrollment_fields;
    }

    /**
     * Determine if email is required for enrolling in this course.
     */
    public function isEmailRequired(): bool
    {
        foreach ($this->effective_enrollment_fields as $field) {
            if (($field['name'] ?? '') === 'email') {
                return !empty($field['is_enabled']) && !empty($field['is_required']);
            }
        }
        return true;
    }

    /**
     * Determine if email field is enabled / displayed for this course.
     */
    public function isEmailEnabled(): bool
    {
        foreach ($this->effective_enrollment_fields as $field) {
            if (($field['name'] ?? '') === 'email') {
                return !empty($field['is_enabled']);
            }
        }
        return true;
    }
}
