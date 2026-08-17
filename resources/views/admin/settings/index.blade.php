@extends('layouts.admin')

@section('content')
<div class="mb-6 md:mb-8">
    <h2 class="text-2xl md:text-3xl font-bold">Global Settings</h2>
    <p class="text-gray-500 mt-1 text-sm md:text-base">Customize the appearance and behavior of the platform.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 max-w-2xl relative pb-24 md:pb-0">
    @csrf

    <!-- Branding Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h3 class="text-lg font-semibold mb-4 border-b border-gray-50 pb-2">Branding</h3>
        
        <div class="space-y-5">
            <!-- Site Name -->
            <div class="focus-ring-primary rounded-lg">
                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                <input type="text" id="site_name" name="site_name" 
                       value="{{ $settings['site_name']->value ?? 'EdTech Platform' }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
            </div>

            <!-- Logo Upload -->
            <div class="focus-ring-primary rounded-lg">
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Site Logo</label>
                @if(isset($settings['site_logo']) && $settings['site_logo']->value)
                    <div class="mb-3 p-3 bg-gray-50 rounded-lg inline-block border border-gray-100">
                        <img src="{{ $settings['site_logo']->value }}" alt="Current Logo" class="h-12 w-auto object-contain">
                    </div>
                @endif
                <input type="file" id="logo" name="logo" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-primary hover:file:bg-green-100 transition-colors cursor-pointer">
            </div>

            <!-- Primary Color -->
            <div class="focus-ring-primary rounded-lg">
                <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-1">Primary Color (Hex)</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="primary_color_picker" 
                           value="{{ $settings['primary_color']->value ?? '#1F6F54' }}"
                           oninput="document.getElementById('primary_color').value = this.value"
                           class="h-12 w-12 rounded cursor-pointer border-0 p-0">
                    <input type="text" id="primary_color" name="primary_color" 
                           value="{{ $settings['primary_color']->value ?? '#1F6F54' }}"
                           oninput="document.getElementById('primary_color_picker').value = this.value"
                           class="flex-1 px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base font-mono">
                </div>
            </div>
        </div>
    </div>

    <!-- Contact & SEO Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h3 class="text-lg font-semibold mb-4 border-b border-gray-50 pb-2">Contact & SEO</h3>
        
        <div class="space-y-5">
            <!-- Contact Email -->
            <div class="focus-ring-primary rounded-lg">
                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Support Email</label>
                <input type="email" id="contact_email" name="contact_email" 
                       value="{{ $settings['contact_email']->value ?? 'support@example.com' }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
            </div>

            <!-- SEO Meta Description -->
            <div class="focus-ring-primary rounded-lg">
                <label for="seo_description" class="block text-sm font-medium text-gray-700 mb-1">SEO Meta Description</label>
                <textarea id="seo_description" name="seo_description" rows="3"
                          class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base resize-none">{{ $settings['seo_description']->value ?? 'Learn anywhere, anytime.' }}</textarea>
            </div>
        </div>
    </div>

    <!-- Submit Action -->
    <div class="fixed bottom-[72px] md:bottom-0 md:static left-0 w-full md:w-auto p-4 md:p-0 bg-white md:bg-transparent border-t border-gray-200 md:border-t-0 z-40 md:mt-6">
        <button type="submit" class="w-full md:w-auto px-6 py-3.5 bg-primary text-white font-semibold rounded-xl shadow-sm hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Save Settings
        </button>
    </div>
</form>
@endsection
