<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Welcome to {{ \App\Models\Setting::getVal('site_name', 'CAB Academy') }}</title>
    <link rel="icon" href="{{ \App\Models\Setting::getVal('site_logo') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-white">
    <div x-data="onboarding()" class="min-h-screen flex flex-col pt-12 pb-28 px-6 max-w-lg mx-auto relative">
        <!-- Progress -->
        <div class="w-full bg-gray-100 rounded-full h-1.5 mb-10">
            <div class="bg-green-600 h-1.5 rounded-full transition-all duration-300" :style="`width: ${(step / totalSteps) * 100}%`"></div>
        </div>

        <div class="flex-1">
            <!-- Step 1 -->
            <div x-show="step === 1" x-transition.opacity>
                <h1 class="text-3xl font-bold mb-2 tracking-tight">Welcome</h1>
                <p class="text-gray-500 mb-8 text-lg">What are you learning?</p>

                <div class="space-y-4">
                    <template x-for="lvl in levels">
                        <button @click="formData.level = lvl" 
                                :class="formData.level === lvl ? 'border-green-600 bg-green-50 text-green-800 ring-1 ring-green-600' : 'border-gray-200 hover:border-gray-300'"
                                class="w-full text-left p-5 rounded-2xl border-2 transition-all flex justify-between items-center group">
                            <span x-text="lvl" class="font-semibold text-lg"></span>
                            <div :class="formData.level === lvl ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Step 2 -->
            <div x-show="step === 2" x-transition.opacity style="display: none;">
                <h1 class="text-3xl font-bold mb-2 tracking-tight">Choose subjects</h1>
                <p class="text-gray-500 mb-8 text-lg">Select what you want to focus on.</p>

                <div class="space-y-4">
                    <template x-for="subj in availableSubjects">
                        <button @click="toggleSubject(subj)" 
                                :class="formData.subjects.includes(subj) ? 'border-green-600 bg-green-50 text-green-800 ring-1 ring-green-600' : 'border-gray-200 hover:border-gray-300'"
                                class="w-full text-left p-5 rounded-2xl border-2 transition-all flex justify-between items-center">
                            <span x-text="subj" class="font-semibold text-lg"></span>
                            <div :class="formData.subjects.includes(subj) ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Step 3 -->
            <div x-show="step === 3" x-transition.opacity style="display: none;">
                <h1 class="text-3xl font-bold mb-2 tracking-tight">Your learning goals</h1>
                <p class="text-gray-500 mb-8 text-lg">Help us tailor your experience.</p>

                <div class="space-y-4">
                    <template x-for="goal in availableGoals">
                        <button @click="toggleGoal(goal)" 
                                :class="formData.goals.includes(goal) ? 'border-green-600 bg-green-50 text-green-800 ring-1 ring-green-600' : 'border-gray-200 hover:border-gray-300'"
                                class="w-full text-left p-5 rounded-2xl border-2 transition-all flex justify-between items-center">
                            <span x-text="goal" class="font-semibold text-lg"></span>
                            <div :class="formData.goals.includes(goal) ? 'opacity-100 scale-100' : 'opacity-0 scale-90'" class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="fixed bottom-0 left-0 w-full p-4 bg-white/95 backdrop-blur-md border-t border-gray-100 flex justify-between safe-area-bottom pb-6">
            <button x-show="step > 1" @click="step--" class="px-6 py-4 rounded-xl font-semibold text-gray-600 hover:bg-gray-100 transition-colors">
                Back
            </button>
            <div x-show="step === 1" class="w-full"></div>
            
            <button x-show="step < totalSteps" @click="step++" :disabled="!canProceed()" class="px-8 py-4 rounded-xl font-semibold text-white bg-green-700 hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm w-full ml-4">
                Next →
            </button>

            <button x-show="step === totalSteps" @click="submit()" :disabled="!canProceed() || loading" class="px-8 py-4 rounded-xl font-semibold text-white bg-green-700 hover:bg-green-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm w-full ml-4 flex items-center justify-center gap-2">
                <span x-show="!loading">Start Learning</span>
                <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('onboarding', () => ({
                step: 1,
                totalSteps: 3,
                loading: false,
                levels: ['School', 'University', 'Professional'],
                availableSubjects: ['Physics', 'Mathematics', 'Chemistry', 'English'],
                availableGoals: ['Improve grades', 'Prepare for exams', 'Build skills'],
                formData: {
                    level: null,
                    subjects: [],
                    goals: []
                },
                toggleSubject(subj) {
                    if (this.formData.subjects.includes(subj)) {
                        this.formData.subjects = this.formData.subjects.filter(s => s !== subj);
                    } else {
                        this.formData.subjects.push(subj);
                    }
                },
                toggleGoal(goal) {
                    if (this.formData.goals.includes(goal)) {
                        this.formData.goals = this.formData.goals.filter(g => g !== goal);
                    } else {
                        this.formData.goals.push(goal);
                    }
                },
                canProceed() {
                    if (this.step === 1) return this.formData.level !== null;
                    if (this.step === 2) return this.formData.subjects.length > 0;
                    if (this.step === 3) return this.formData.goals.length > 0;
                    return false;
                },
                async submit() {
                    this.loading = true;
                    try {
                        const response = await fetch('/onboarding', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.formData)
                        });
                        const data = await response.json();
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    } catch (error) {
                        console.error('Submission failed', error);
                        this.loading = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>
