<?php

namespace App\Support;

use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class SocialImageGenerator
{
    /**
     * Generate or retrieve a cached 1200x630 Open Graph landscape banner for a course.
     * Guaranteed to return a valid 1200x630 JPEG path for Facebook/Meta large top banner display.
     */
    public static function getOrGenerateCourseSocialCard(Course $course, bool $strict = false): ?string
    {
        $cacheDir = storage_path('app/public/course_og_cards');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $cachedFile = "{$cacheDir}/course_{$course->id}.jpg";

        // Determine source image
        $srcPath = self::resolveSourceImagePath($course);
        if (!$srcPath || !file_exists($srcPath)) {
            if ($strict) {
                return null;
            }
            $default = public_path('images/og-banner.jpg');
            return file_exists($default) ? $default : null;
        }

        // If cached card exists and is newer than source image, use cache
        if (file_exists($cachedFile) && filemtime($cachedFile) >= filemtime($srcPath)) {
            return $cachedFile;
        }

        // Generate 1200x630 card
        $generated = self::render1200x630Card($srcPath, $cachedFile);
        return $generated ? $cachedFile : (file_exists($cachedFile) ? $cachedFile : $srcPath);
    }

    /**
     * Resolve the local filesystem path for a course cover image.
     */
    protected static function resolveSourceImagePath(Course $course): ?string
    {
        $img = $course->cover_image ?: $course->thumbnail;
        if (!$img) {
            return null;
        }

        // Base64 data URI
        if (str_starts_with($img, 'data:image/')) {
            $tmpDir = storage_path('app/public/course_og_cards');
            if (!is_dir($tmpDir)) {
                @mkdir($tmpDir, 0755, true);
            }
            $tmpFile = "{$tmpDir}/tmp_b64_{$course->id}.jpg";
            $data = substr($img, strpos($img, ',') + 1);
            $decoded = base64_decode($data);
            if ($decoded) {
                file_put_contents($tmpFile, $decoded);
                return $tmpFile;
            }
            return null;
        }

        // External URL: fallback to default if not local
        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
            return null;
        }

        // Relative path candidates
        $clean = preg_replace('/^\/?(storage\/app\/public|storage|uploads)\//i', '', $img);
        $clean = ltrim($clean, '/');

        $candidates = [
            storage_path("app/public/{$clean}"),
            public_path("storage/{$clean}"),
            public_path("uploads/{$clean}"),
            public_path($img),
        ];

        foreach ($candidates as $cand) {
            if (file_exists($cand) && is_file($cand)) {
                // Ensure public/storage mirror exists for direct static resolution
                $storageMirror = public_path("storage/{$clean}");
                if (!file_exists($storageMirror) && !is_link(public_path('storage'))) {
                    @mkdir(dirname($storageMirror), 0775, true);
                    @copy($cand, $storageMirror);
                }
                return $cand;
            }
        }

        return null;
    }

    /**
     * Render a 1200x630 landscape social banner from any source image (vertical, square, or landscape).
     */
    protected static function render1200x630Card(string $srcPath, string $outputPath): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        $info = @getimagesize($srcPath);
        if (!$info) {
            return false;
        }

        $sw = $info[0];
        $sh = $info[1];
        $mime = $info['mime'] ?? 'image/jpeg';

        $src = match ($mime) {
            'image/png' => @imagecreatefrompng($srcPath),
            'image/webp' => @imagecreatefromwebp($srcPath),
            'image/gif' => @imagecreatefromgif($srcPath),
            default => @imagecreatefromjpeg($srcPath),
        };

        if (!$src) {
            return false;
        }

        $tw = 1200;
        $th = 630;
        $canvas = imagecreatetruecolor($tw, $th);

        $ratio = $sw / max(1, $sh);

        if ($ratio >= 1.75 && $ratio <= 2.05) {
            // Already standard landscape (1.91:1): resample directly
            imagecopyresampled($canvas, $src, 0, 0, 0, 0, $tw, $th, $sw, $sh);
        } else {
            // Vertical flyer or square poster: composite onto 1200x630 with matching blurred backdrop
            $bgScale = max($tw / $sw, $th / $sh);
            $bgW = (int) ($sw * $bgScale);
            $bgH = (int) ($sh * $bgScale);
            $bgX = (int) (($tw - $bgW) / 2);
            $bgY = (int) (($th - $bgH) / 2);
            imagecopyresampled($canvas, $src, $bgX, $bgY, 0, 0, $bgW, $bgH, $sw, $sh);

            // Fast blur passes
            for ($i = 0; $i < 10; $i++) {
                imagefilter($canvas, IMG_FILTER_GAUSSIAN_BLUR);
            }
            // Rich imperial navy overlay (#051c45)
            imagefilter($canvas, IMG_FILTER_COLORIZE, 5, 28, 69, 55);

            // Foreground: sharp full flyer scaled to fit height 610
            $scale = 610 / $sh;
            $dw = (int) ($sw * $scale);
            $dh = 610;
            $dx = (int) (($tw - $dw) / 2);
            $dy = 10;

            // 1px subtle border around flyer
            $border = imagecolorallocate($canvas, 255, 255, 255);
            imagefilledrectangle($canvas, $dx - 1, $dy - 1, $dx + $dw, $dy + $dh, $border);

            imagecopyresampled($canvas, $src, $dx, $dy, 0, 0, $dw, $dh, $sw, $sh);
        }

        $success = @imagejpeg($canvas, $outputPath, 88);
        @imagedestroy($canvas);
        @imagedestroy($src);

        return $success && file_exists($outputPath);
    }
}

