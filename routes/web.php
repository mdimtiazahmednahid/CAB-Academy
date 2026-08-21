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
    $courses = \App\Models\Course::where('is_published', true)->latest()->take(3)->get();
    return view('welcome', compact('courses'));
});

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
        
        return view('dashboard', compact('payments', 'enrolledCourses', 'jobApplications'));
    })->name('dashboard');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store']);

    // Catalog & Performance
    Route::get('/catalog', [App\Http\Controllers\CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/performance', [App\Http\Controllers\PerformanceController::class, 'index'])->name('performance.index');
    Route::get('/jobs', [App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
    Route::post('/jobs/{job}/apply', [App\Http\Controllers\JobApplicationController::class, 'store'])->name('jobs.apply');

    // Courses
    Route::get('/courses/{course}', [App\Http\Controllers\CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/enroll', [App\Http\Controllers\CourseController::class, 'enroll'])->name('courses.enroll');
    Route::get('/courses/{course}/lessons/{lesson}', [App\Http\Controllers\CourseController::class, 'showLesson'])->name('lessons.show');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [App\Http\Controllers\CourseController::class, 'completeLesson'])->name('lessons.complete');
    Route::get('/courses/{course}/checkout', [App\Http\Controllers\PaymentController::class, 'checkout'])->name('courses.checkout');
    Route::post('/courses/{course}/checkout', [App\Http\Controllers\PaymentController::class, 'store'])->name('courses.checkout.store');
    Route::get('/courses/{course}/quizzes/{quiz}', [App\Http\Controllers\QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/courses/{course}/quizzes/{quiz}', [App\Http\Controllers\QuizController::class, 'store'])->name('quizzes.store');
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

    Route::middleware([\App\Http\Middleware\IsStrictAdmin::class])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
        Route::post('/settings', [SettingsController::class, 'store'])->name('admin.settings.store');
        
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
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
