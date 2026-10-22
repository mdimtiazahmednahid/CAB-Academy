<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
Route::get('/reset-db', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    return 'Database optimized, cleared, and migrated successfully!';
});

Route::get('/', function () {
    $courses = \App\Models\Course::where('is_published', true)->latest()->get();
    
    $certCount = (int) \App\Models\Setting::getVal('landing_cert_count', 4);
    if ($certCount < 1) $certCount = 4;
    $dayLongCount = (int) \App\Models\Setting::getVal('landing_daylong_count', 4);
    if ($dayLongCount < 1) $dayLongCount = 4;
    $blogCount = (int) \App\Models\Setting::getVal('landing_blog_count', 3);
    if ($blogCount < 1) $blogCount = 3;

    // Certificate Courses Section
    $certificateCourses = \App\Models\Course::where('is_published', true)
        ->where(function($q) {
            $q->where('title', 'like', '%Certificate%')
              ->orWhere('title', 'like', '%L/C%')
              ->orWhere('title', 'like', '%Supply Chain%')
              ->orWhere('title', 'like', '%VAT%')
              ->orWhere('title', 'like', '%Financial%')
              ->orWhere('level', 'like', '%Certificate%');
        })->take($certCount)->get();

    if ($certificateCourses->count() < 2) {
        $certificateCourses = $courses->take($certCount);
    }

    // Day Long Courses Section
    $dayLongCourses = \App\Models\Course::where('is_published', true)
        ->where(function($q) {
            $q->where('title', 'like', '%Commercial%')
              ->orWhere('title', 'like', '%Labor%')
              ->orWhere('title', 'like', '%Civil%')
              ->orWhere('title', 'like', '%Time%')
              ->orWhere('duration', 'like', '%Day%');
        })->take($dayLongCount)->get();

    if ($dayLongCourses->count() < 2) {
        $dayLongCourses = $courses->skip(2)->take($dayLongCount);
    }

    $jobs = \App\Models\JobPost::where('is_active', true)->latest()->take(3)->get();
    $recentBlogs = \App\Models\BlogPost::published()->latest('published_at')->take($blogCount)->get();
    return view('welcome', compact('courses', 'certificateCourses', 'dayLongCourses', 'jobs', 'recentBlogs'));
});

// Community Feed & Blogs (Minimal Community Like Facebook)
Route::get('/feed', [App\Http\Controllers\FeedController::class, 'index'])->name('feed.index');
Route::post('/feed/posts', [App\Http\Controllers\FeedController::class, 'store'])->name('feed.posts.store');
Route::post('/feed/posts/{post}/like', [App\Http\Controllers\FeedController::class, 'like'])->name('feed.posts.like');
Route::post('/feed/posts/{post}/comments', [App\Http\Controllers\FeedController::class, 'comment'])->name('feed.posts.comments.store');
Route::delete('/feed/posts/{post}', [App\Http\Controllers\FeedController::class, 'destroy'])->name('feed.posts.destroy');
Route::get('/community', function () {
    return redirect()->route('feed.index');
})->name('community.index');

// Blog & Knowledge Hub Routes
Route::get('/blog', [App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Institutional Trust & Legal Suite
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');
Route::get('/about', function () { return view('about.index'); })->name('about.index');
Route::get('/privacy-policy', function () { return view('legal.privacy'); })->name('legal.privacy');
Route::get('/terms-of-service', function () { return view('legal.terms'); })->name('legal.terms');
Route::get('/refund-policy', function () { return view('legal.refund'); })->name('legal.refund');

// Student Wishlist & Saved Courses
Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/{course}/toggle', [App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');

// Public Verifiable Certificate Registry
Route::get('/verify-certificate/{certificate_number}', [App\Http\Controllers\CertificateController::class, 'verify'])->name('certificate.verify');

// Executive Instructors & Faculty Directory
Route::get('/instructors', [App\Http\Controllers\InstructorController::class, 'index'])->name('instructors.index');
Route::get('/faculty', [App\Http\Controllers\InstructorController::class, 'index'])->name('faculty.index');

// Direct course type filter shortcuts
Route::get('/certificate-course', function () {
    return redirect()->route('catalog.index', ['type' => 'certificate']);
})->name('catalog.certificate');

Route::get('/day-long-course', function () {
    return redirect()->route('catalog.index', ['type' => 'day_long']);
})->name('catalog.day_long');

// InfinityFree Storage Symlink Workaround: Stream media files dynamically without requiring symlinks
Route::get('/storage/{folder}/{file}', function ($folder, $file) {
    $filePath = storage_path("app/public/{$folder}/" . $file);
    if (!file_exists($filePath)) {
        abort(404);
    }
    $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('file', '.*')->name('storage.stream');

// Public Course Catalog & Category Routes
Route::get('/catalog', [App\Http\Controllers\CatalogController::class, 'index'])->name('catalog.index');
Route::get('/courses', [App\Http\Controllers\CatalogController::class, 'index'])->name('courses.index');
Route::get('/category/{category}', [App\Http\Controllers\CatalogController::class, 'category'])->name('catalog.category');
Route::get('/export-import', function () {
    return redirect()->route('catalog.category', ['category' => 'export-import']);
});

// Public Course Overview & Enrollment Initiator
Route::get('/courses/{course}', [App\Http\Controllers\CourseController::class, 'show'])->name('courses.show');
Route::post('/courses/{course}/enroll', [App\Http\Controllers\CourseController::class, 'enroll'])->name('courses.enroll');
Route::post('/courses/{course}/express-enroll', [App\Http\Controllers\PaymentController::class, 'expressEnroll'])->name('courses.express_enroll');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (auth()->user()->preferences === null) {
            return redirect()->route('onboarding');
        }
        $user = auth()->user();
        $payments = $user->payments()->with(['course'])->latest()->get();
        $enrolledCourses = $user->enrolledCourses()->latest()->get();
        $jobApplications = $user->jobApplications()->with('jobPost')->latest()->get();
        
        $feedCourses = \App\Models\Course::where('is_published', true)->latest()->take(3)->get();
        $feedJobs = \App\Models\JobPost::where('is_active', true)->latest()->take(3)->get();
        
        return view('dashboard', compact('payments', 'enrolledCourses', 'jobApplications', 'feedCourses', 'feedJobs'));
    })->name('dashboard');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store']);

    // Performance & Jobs
    Route::get('/performance', [App\Http\Controllers\PerformanceController::class, 'index'])->name('performance.index');
    Route::get('/jobs', [App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
    Route::post('/jobs/{job}/apply', [App\Http\Controllers\JobApplicationController::class, 'store'])->name('jobs.apply');

    // Authenticated Learning & Quizzes
    Route::get('/courses/{course}/lessons/{lesson}', [App\Http\Controllers\CourseController::class, 'showLesson'])->name('lessons.show');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [App\Http\Controllers\CourseController::class, 'completeLesson'])->name('lessons.complete');
    Route::get('/courses/{course}/checkout', [App\Http\Controllers\PaymentController::class, 'checkout'])->name('courses.checkout');
    Route::post('/courses/{course}/checkout', [App\Http\Controllers\PaymentController::class, 'store'])->name('courses.checkout.store');
    Route::get('/courses/{course}/quizzes/{quiz}', [App\Http\Controllers\QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/courses/{course}/quizzes/{quiz}', [App\Http\Controllers\QuizController::class, 'store'])->name('quizzes.store');

    // Digital Certificate & Printable Tuition Invoice
    Route::get('/courses/{course}/certificate', [App\Http\Controllers\CertificateController::class, 'show'])->name('courses.certificate');
    Route::get('/payments/{payment}/invoice', [App\Http\Controllers\InvoiceController::class, 'show'])->name('payments.invoice');
});

Route::prefix('admin')->middleware(['auth', 'verified', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    // Courses
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('admin.courses');
    Route::post('/courses', [AdminCourseController::class, 'store']);
    Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->name('admin.courses.show');
    Route::put('/courses/{course}', [AdminCourseController::class, 'update'])->name('admin.courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');
    
    Route::post('/courses/{course}/sections', [AdminCourseController::class, 'storeSection'])->name('admin.courses.sections.store');
    Route::put('/courses/{course}/sections/{section}', [AdminCourseController::class, 'updateSection'])->name('admin.courses.sections.update');
    Route::delete('/courses/{course}/sections/{section}', [AdminCourseController::class, 'destroySection'])->name('admin.courses.sections.destroy');
    
    Route::post('/courses/{course}/sections/{section}/lessons', [AdminCourseController::class, 'storeLesson'])->name('admin.courses.lessons.store');
    Route::put('/courses/{course}/sections/{section}/lessons/{lesson}', [AdminCourseController::class, 'updateLesson'])->name('admin.courses.lessons.update');
    Route::delete('/courses/{course}/sections/{section}/lessons/{lesson}', [AdminCourseController::class, 'destroyLesson'])->name('admin.courses.lessons.destroy');
    
    Route::post('/courses/{course}/materials', [AdminCourseController::class, 'storeMaterial'])->name('admin.courses.materials.store');
    Route::delete('/courses/{course}/materials/{material}', [AdminCourseController::class, 'destroyMaterial'])->name('admin.courses.materials.destroy');

    // Quizzes
    Route::post('/courses/{course}/quizzes', [AdminQuizController::class, 'store'])->name('admin.courses.quizzes.store');
    Route::put('/courses/{course}/quizzes/{quiz}', [AdminQuizController::class, 'update'])->name('admin.courses.quizzes.update');
    Route::delete('/courses/{course}/quizzes/{quiz}', [AdminQuizController::class, 'destroy'])->name('admin.courses.quizzes.destroy');
    Route::get('/courses/{course}/quizzes/{quiz}', [AdminQuizController::class, 'show'])->name('admin.quizzes.show');
    Route::post('/courses/{course}/quizzes/{quiz}/questions', [AdminQuizController::class, 'storeQuestion'])->name('admin.quizzes.questions.store');
    Route::delete('/courses/{course}/quizzes/{quiz}/questions/{question}', [AdminQuizController::class, 'destroyQuestion'])->name('admin.quizzes.questions.destroy');

    // Payments
    Route::resource('payment-methods', App\Http\Controllers\Admin\PaymentMethodController::class)->except(['create', 'show', 'edit'])->names('admin.payment-methods');
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments.index');
    Route::post('/payments/{payment}/status', [App\Http\Controllers\Admin\PaymentController::class, 'updateStatus'])->name('admin.payments.updateStatus');

    // Inquiries Inbox
    Route::get('/inquiries', [App\Http\Controllers\Admin\InquiryController::class, 'index'])->name('admin.inquiries.index');
    Route::get('/inquiries/{inquiry}', [App\Http\Controllers\Admin\InquiryController::class, 'show'])->name('admin.inquiries.show');
    Route::patch('/inquiries/{inquiry}/status', [App\Http\Controllers\Admin\InquiryController::class, 'updateStatus'])->name('admin.inquiries.status');
    Route::delete('/inquiries/{inquiry}', [App\Http\Controllers\Admin\InquiryController::class, 'destroy'])->name('admin.inquiries.destroy');

    // Articles & Knowledge Hub
    Route::resource('blogs', App\Http\Controllers\Admin\BlogController::class)->names('admin.blogs');
    
    // Executive Instructors Management
    Route::resource('instructors', App\Http\Controllers\Admin\InstructorController::class)->names('admin.instructors');

    Route::middleware([\App\Http\Middleware\IsStrictAdmin::class])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
        Route::post('/settings', [SettingsController::class, 'store'])->name('admin.settings.store');
        
        Route::get('/announcements', [\App\Http\Controllers\Admin\AnnouncementController::class, 'create'])->name('admin.announcements.create');
        Route::post('/announcements', [\App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('admin.announcements.store');
        
        Route::get('/frontend', [App\Http\Controllers\Admin\FrontendController::class, 'index'])->name('admin.frontend.index');
        Route::post('/frontend', [App\Http\Controllers\Admin\FrontendController::class, 'store'])->name('admin.frontend.store');
        
        // Users
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users');
        Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
        Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::put('/users/{user}/preferences', [App\Http\Controllers\Admin\UserController::class, 'updatePreferences'])->name('admin.users.preferences.update');
        
        // Jobs
        Route::get('/jobs', [App\Http\Controllers\Admin\JobController::class, 'index'])->name('admin.jobs');
        Route::post('/jobs', [App\Http\Controllers\Admin\JobController::class, 'store'])->name('admin.jobs.store');
        Route::get('/jobs/{job}', [App\Http\Controllers\Admin\JobController::class, 'show'])->name('admin.jobs.show');
        Route::put('/jobs/{job}', [App\Http\Controllers\Admin\JobController::class, 'update'])->name('admin.jobs.update');
        Route::delete('/jobs/{job}', [App\Http\Controllers\Admin\JobController::class, 'destroy'])->name('admin.jobs.destroy');
        // Trash / Recycle Bin
        Route::get('/trash', [App\Http\Controllers\Admin\TrashController::class, 'index'])->name('admin.trash.index');
        Route::post('/trash/{type}/{id}/restore', [App\Http\Controllers\Admin\TrashController::class, 'restore'])->name('admin.trash.restore');
        Route::delete('/trash/{type}/{id}/force', [App\Http\Controllers\Admin\TrashController::class, 'forceDelete'])->name('admin.trash.forceDelete');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});

require __DIR__.'/auth.php';
