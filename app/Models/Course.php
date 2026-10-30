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
     * Concise, social-optimized short link for this course (e.g. https://cabacademy.bd/c/5).
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
     * Pre-formatted WhatsApp share link with course title and short URL.
     */
    public function getWhatsappShareUrlAttribute(): string
    {
        $text = $this->title . " - Join this program at CAB Academy:\n" . $this->short_url;
        return 'https://api.whatsapp.com/send?text=' . urlencode($text);
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
     * Pre-formatted WhatsApp Academic Counselor direct message URL.
     */
    public function getCounselorWhatsappUrlAttribute(): string
    {
        $phone = '8801917054664';
        $message = "Assalamu Alaikum CAB Academy! I would like to inquire/enroll in *{$this->title}* (Fee: ৳" . number_format($this->price, 0) . "). Please share batch details and enrollment procedure: " . $this->short_url;
        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
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
        return $lead ? $lead->name : 'CAB Academy Senior Faculty';
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
                'badge' => 'Default',
                'description' => 'Name, Phone, and Required Email. Optimal for LMS access, notifications, and verifiable digital certificates.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '01917054664'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'name@company.com'],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                ],
            ],
            'quick_lead' => [
                'key' => 'quick_lead',
                'name' => 'Quick Lead (No Email Required)',
                'badge' => 'Highest Velocity',
                'description' => 'Fastest conversion: Name + Phone only. Email is not required or asked for. Perfect for urgent workshops.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '01917054664'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => false, 'is_enabled' => false, 'is_system' => true, 'placeholder' => 'name@company.com'],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                ],
            ],
            'corporate' => [
                'key' => 'corporate',
                'name' => 'Corporate & Executive',
                'badge' => 'B2B & Executive',
                'description' => 'Name, Phone, Email, Organization / Company, and Designation for executive billing.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '01917054664'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'name@company.com'],
                    ['name' => 'organization', 'label' => 'Organization / Company', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Apex Footwear Ltd.'],
                    ['name' => 'designation', 'label' => 'Designation / Title', 'type' => 'text', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Senior Commercial Manager'],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                ],
            ],
            'comprehensive' => [
                'key' => 'comprehensive',
                'name' => 'Comprehensive Application',
                'badge' => 'Full Dossier',
                'description' => 'In-depth application: Name, Phone, Email, Organization, Designation, City, and Expectations.',
                'fields' => [
                    ['name' => 'name', 'label' => 'Full Name', 'type' => 'text', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'e.g. Tanvir Ahmed'],
                    ['name' => 'phone', 'label' => 'Mobile (11 Digits)', 'type' => 'tel', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => '01917054664'],
                    ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'placeholder' => 'name@company.com'],
                    ['name' => 'organization', 'label' => 'Organization / Company', 'type' => 'text', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Beximco Group'],
                    ['name' => 'designation', 'label' => 'Designation / Role', 'type' => 'text', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Supply Chain Officer'],
                    ['name' => 'city', 'label' => 'City / District', 'type' => 'text', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'e.g. Dhaka / Chattogram'],
                    ['name' => 'training_mode', 'label' => 'Preferred Attendance Mode', 'type' => 'select', 'is_required' => true, 'is_enabled' => true, 'is_system' => true, 'options' => ['online_zoom' => 'Live Online (Zoom)', 'in_person' => 'Executive Campus']],
                    ['name' => 'comments', 'label' => 'Expectations / Queries', 'type' => 'textarea', 'is_required' => false, 'is_enabled' => true, 'is_system' => false, 'placeholder' => 'Any specific customs or tax topics you want covered?'],
                ],
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
