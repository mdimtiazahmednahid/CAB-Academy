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
            $q->where('level', 'Certificate')
              ->orWhere('title', 'like', '%Certificate%');
        })
        ->orderBy('id', 'asc')
        ->take($certCount)
        ->get();

    if ($certificateCourses->count() < 2) {
        $certificateCourses = $courses->take($certCount);
    }

    // Day Long Courses Section (strictly non-overlapping with Certificate programs)
    $certIds = $certificateCourses->pluck('id')->toArray();
    $dayLongCourses = \App\Models\Course::where('is_published', true)
        ->whereNotIn('id', $certIds)
        ->where(function($q) {
            $q->where('level', 'Day Long')
              ->orWhere('duration', 'like', '%Day%')
              ->orWhere('title', 'like', '%Day-Long%')
              ->orWhere('title', 'like', '%Day Long%')
              ->orWhere('title', 'like', '%Masterclass%');
        })
        ->orderBy('id', 'asc')
        ->take($dayLongCount)
        ->get();

    if ($dayLongCourses->count() < 2) {
        $dayLongCourses = $courses->whereNotIn('id', $certIds)->take($dayLongCount);
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

// Career & Placement Opportunities (Public Portal)
Route::get('/jobs', [App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
Route::get('/careers', [App\Http\Controllers\JobController::class, 'index'])->name('careers.index');

// Direct course type filter shortcuts
Route::get('/certificate-course', function () {
    return redirect()->route('catalog.index', ['type' => 'certificate']);
})->name('catalog.certificate');

Route::get('/day-long-course', function () {
    return redirect()->route('catalog.index', ['type' => 'day_long']);
})->name('catalog.day_long');

// InfinityFree Storage Symlink Workaround: Stream media files dynamically without requiring symlinks
Route::get('/storage/{path}', function ($path) {
    // 1. Primary storage path under storage/app/public/
    $filePath = storage_path("app/public/" . $path);
    if (!file_exists($filePath) || is_dir($filePath)) {
        // 2. Fallback checking public/storage/ directory
        $filePath = public_path("storage/" . $path);
    }
    if (!file_exists($filePath) || is_dir($filePath)) {
        abort(404);
    }
    $mimeType = @mime_content_type($filePath) ?: 'application/octet-stream';
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.stream');

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

    // Performance & Job Applications
    Route::get('/performance', [App\Http\Controllers\PerformanceController::class, 'index'])->name('performance.index');
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

Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    // Courses
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('admin.courses');
    Route::post('/courses', [AdminCourseController::class, 'store'])->name('admin.courses.store');
    Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->name('admin.courses.show');
    Route::match(['put', 'post'], '/courses/{course}', [AdminCourseController::class, 'update'])->name('admin.courses.update');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');
    
    Route::post('/courses/{course}/sections', [AdminCourseController::class, 'storeSection'])->name('admin.courses.sections.store');
    Route::match(['put', 'post'], '/courses/{course}/sections/{section}', [AdminCourseController::class, 'updateSection'])->name('admin.courses.sections.update');
    Route::delete('/courses/{course}/sections/{section}', [AdminCourseController::class, 'destroySection'])->name('admin.courses.sections.destroy');
    
    Route::post('/courses/{course}/sections/{section}/lessons', [AdminCourseController::class, 'storeLesson'])->name('admin.courses.lessons.store');
    Route::match(['put', 'post'], '/courses/{course}/sections/{section}/lessons/{lesson}', [AdminCourseController::class, 'updateLesson'])->name('admin.courses.lessons.update');
    Route::delete('/courses/{course}/sections/{section}/lessons/{lesson}', [AdminCourseController::class, 'destroyLesson'])->name('admin.courses.lessons.destroy');
    
    Route::post('/courses/{course}/materials', [AdminCourseController::class, 'storeMaterial'])->name('admin.courses.materials.store');
    Route::delete('/courses/{course}/materials/{material}', [AdminCourseController::class, 'destroyMaterial'])->name('admin.courses.materials.destroy');

    // Certificate Issuance & Revocation for Students
    Route::post('/courses/{course}/students/{student}/certificate', [AdminCourseController::class, 'issueCertificate'])->name('admin.courses.certificates.issue');
    Route::delete('/courses/{course}/students/{student}/certificate', [AdminCourseController::class, 'revokeCertificate'])->name('admin.courses.certificates.revoke');

    // Quizzes
    Route::post('/courses/{course}/quizzes', [AdminQuizController::class, 'store'])->name('admin.courses.quizzes.store');
    Route::match(['put', 'post'], '/courses/{course}/quizzes/{quiz}', [AdminQuizController::class, 'update'])->name('admin.courses.quizzes.update');
    Route::delete('/courses/{course}/quizzes/{quiz}', [AdminQuizController::class, 'destroy'])->name('admin.courses.quizzes.destroy');
    Route::get('/courses/{course}/quizzes/{quiz}', [AdminQuizController::class, 'show'])->name('admin.quizzes.show');
    Route::post('/courses/{course}/quizzes/{quiz}/questions', [AdminQuizController::class, 'storeQuestion'])->name('admin.quizzes.questions.store');
    Route::delete('/courses/{course}/quizzes/{quiz}/questions/{question}', [AdminQuizController::class, 'destroyQuestion'])->name('admin.quizzes.questions.destroy');

    // Payments
    Route::match(['put', 'post'], '/payment-methods/{paymentMethod}', [App\Http\Controllers\Admin\PaymentMethodController::class, 'update'])->name('admin.payment-methods.update');
    Route::resource('payment-methods', App\Http\Controllers\Admin\PaymentMethodController::class)->except(['create', 'show', 'edit', 'update'])->names('admin.payment-methods');
    Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('admin.payments.index');
    Route::match(['post', 'put'], '/payments/{payment}/status', [App\Http\Controllers\Admin\PaymentController::class, 'updateStatus'])->name('admin.payments.updateStatus');

    // Inquiries Inbox
    Route::get('/inquiries', [App\Http\Controllers\Admin\InquiryController::class, 'index'])->name('admin.inquiries.index');
    Route::get('/inquiries/{inquiry}', [App\Http\Controllers\Admin\InquiryController::class, 'show'])->name('admin.inquiries.show');
    Route::match(['patch', 'post'], '/inquiries/{inquiry}/status', [App\Http\Controllers\Admin\InquiryController::class, 'updateStatus'])->name('admin.inquiries.status');
    Route::delete('/inquiries/{inquiry}', [App\Http\Controllers\Admin\InquiryController::class, 'destroy'])->name('admin.inquiries.destroy');

    // Accounting & Treasury
    Route::get('/accounting', [App\Http\Controllers\Admin\AccountingController::class, 'index'])->name('admin.accounting.index');
    Route::post('/accounting/tuition', [App\Http\Controllers\Admin\AccountingController::class, 'recordTuition'])->name('admin.accounting.tuition.store');
    Route::post('/accounting/expenses', [App\Http\Controllers\Admin\AccountingController::class, 'recordExpense'])->name('admin.accounting.expenses.store');
    Route::delete('/accounting/expenses/{expense}', [App\Http\Controllers\Admin\AccountingController::class, 'destroyExpense'])->name('admin.accounting.expenses.destroy');
    Route::get('/accounting/statement', [App\Http\Controllers\Admin\AccountingController::class, 'statement'])->name('admin.accounting.statement');

    // Community Feed Moderation
    Route::get('/feeds', [App\Http\Controllers\Admin\FeedController::class, 'index'])->name('admin.feeds.index');
    Route::match(['patch', 'post'], '/feeds/{post}/pin', [App\Http\Controllers\Admin\FeedController::class, 'togglePin'])->name('admin.feeds.pin');
    Route::post('/feeds/broadcast', [App\Http\Controllers\Admin\FeedController::class, 'storeBroadcast'])->name('admin.feeds.broadcast');
    Route::delete('/feeds/{post}', [App\Http\Controllers\Admin\FeedController::class, 'destroy'])->name('admin.feeds.destroy');
    Route::delete('/feeds/comments/{comment}', [App\Http\Controllers\Admin\FeedController::class, 'destroyComment'])->name('admin.feeds.comments.destroy');

    // Articles & Knowledge Hub
    Route::match(['patch', 'post'], '/blogs/{blog}/feature', [App\Http\Controllers\Admin\BlogController::class, 'toggleFeatured'])->name('admin.blogs.feature');
    Route::resource('blogs', App\Http\Controllers\Admin\BlogController::class)->except(['update'])->names('admin.blogs');
    Route::match(['put', 'post'], '/blogs/{blog}', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('admin.blogs.update');
    
    // Executive Instructors Management
    Route::resource('instructors', App\Http\Controllers\Admin\InstructorController::class)->except(['update'])->names('admin.instructors');
    Route::match(['put', 'post'], '/instructors/{instructor}', [App\Http\Controllers\Admin\InstructorController::class, 'update'])->name('admin.instructors.update');

    // Course Categories Management
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->except(['update'])->names('admin.categories');
    Route::match(['put', 'post'], '/categories/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('admin.categories.update');

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
        Route::match(['put', 'post'], '/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
        Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::match(['put', 'post'], '/users/{user}/preferences', [App\Http\Controllers\Admin\UserController::class, 'updatePreferences'])->name('admin.users.preferences.update');
        
        // Jobs
        Route::get('/jobs', [App\Http\Controllers\Admin\JobController::class, 'index'])->name('admin.jobs');
        Route::post('/jobs', [App\Http\Controllers\Admin\JobController::class, 'store'])->name('admin.jobs.store');
        Route::get('/jobs/{job}', [App\Http\Controllers\Admin\JobController::class, 'show'])->name('admin.jobs.show');
        Route::match(['put', 'post'], '/jobs/{job}', [App\Http\Controllers\Admin\JobController::class, 'update'])->name('admin.jobs.update');
        Route::delete('/jobs/{job}', [App\Http\Controllers\Admin\JobController::class, 'destroy'])->name('admin.jobs.destroy');
        // Trash / Recycle Bin
        Route::get('/trash', [App\Http\Controllers\Admin\TrashController::class, 'index'])->name('admin.trash.index');
        Route::post('/trash/{type}/{id}/restore', [App\Http\Controllers\Admin\TrashController::class, 'restore'])->name('admin.trash.restore');
        Route::match(['delete', 'post'], '/trash/{type}/{id}/force', [App\Http\Controllers\Admin\TrashController::class, 'forceDelete'])->name('admin.trash.forceDelete');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['patch', 'post', 'put'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::match(['patch', 'post', 'put'], '/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
});

require __DIR__.'/auth.php';
