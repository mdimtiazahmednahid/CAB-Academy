<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number',
        'user_id',
        'course_id',
        'issue_date',
        'verification_hash',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public static function generateFor(User $user, Course $course): self
    {
        $existing = self::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $certificateNumber = 'CAB-' . date('Y') . '-' . strtoupper(substr(md5($user->id . '_' . $course->id . '_' . time()), 0, 8));

        return self::create([
            'certificate_number' => $certificateNumber,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'issue_date' => now()->toDateString(),
            'verification_hash' => hash('sha256', $certificateNumber . '_' . $user->id . '_' . $course->id),
        ]);
    }
}
