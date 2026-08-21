<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jobs = \App\Models\JobPost::all();
echo "Total Jobs: " . $jobs->count() . "<br>";
foreach ($jobs as $job) {
    echo "Job: " . $job->title . " | Active: " . ($job->is_active ? 'Yes' : 'No') . "<br>";
}

$activeJobs = \App\Models\JobPost::where('is_active', true)->latest()->take(3)->get();
echo "Query Result: " . $activeJobs->count() . "<br>";
