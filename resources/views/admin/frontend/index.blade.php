@extends('layouts.admin')

@section('content')
<div class="mb-6 md:mb-8">
    <h2 class="text-2xl md:text-3xl font-bold">Landing Page Customizer</h2>
    <p class="text-gray-500 mt-1 text-sm md:text-base">Customize the text and features on the public home page.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg border border-green-200 text-sm font-medium">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.frontend.store') }}" method="POST" class="space-y-8 max-w-4xl relative pb-24 md:pb-0">
    @csrf

    <!-- Landing Page Mode -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
        <label for="landing_page_mode" class="block text-sm font-medium text-gray-700 mb-2">Landing Page Mode</label>
        <select id="landing_page_mode" name="landing_page_mode" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary" onchange="toggleMode()">
            <option value="default" {{ ($settings['landing_page_mode'] ?? 'default') === 'default' ? 'selected' : '' }}>Default Template</option>
            <option value="custom" {{ ($settings['landing_page_mode'] ?? 'default') === 'custom' ? 'selected' : '' }}>Custom HTML</option>
        </select>
        <p class="mt-2 text-xs text-gray-500">Choose "Custom HTML" to write your own raw HTML code instead of using the default block design.</p>
    </div>

    <!-- Custom HTML Section -->
    <div id="custom_html_section" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6 {{ ($settings['landing_page_mode'] ?? 'default') === 'default' ? 'hidden' : '' }}">
        <div class="flex justify-between items-center mb-4 border-b border-gray-50 pb-2">
            <h3 class="text-lg font-semibold">Custom HTML Code</h3>
        </div>
        <div>
            <textarea name="landing_custom_html" rows="20" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-sm font-mono bg-gray-50" placeholder="<!DOCTYPE html>&#10;<html>&#10;  <head>&#10;    ...&#10;  </head>&#10;  <body>&#10;    ...&#10;  </body>&#10;</html>">{{ $settings['landing_custom_html'] ?? '' }}</textarea>
        </div>
    </div>

    <!-- Default Template Sections -->
    <div id="default_template_sections" class="space-y-8 {{ ($settings['landing_page_mode'] ?? 'default') === 'custom' ? 'hidden' : '' }}">
        <!-- Hero Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-50 pb-2">Hero Section</h3>
            
            <div class="space-y-5">
                <div>
                    <label for="landing_hero_title" class="block text-sm font-medium text-gray-700 mb-1">Hero Title</label>
                    <input type="text" id="landing_hero_title" name="landing_hero_title" 
                           value="{{ $settings['landing_hero_title'] ?? 'Unlock Your Full Potential' }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
                </div>

                <div>
                    <label for="landing_hero_subtitle" class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle</label>
                    <textarea id="landing_hero_subtitle" name="landing_hero_subtitle" rows="2"
                              class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base resize-none">{{ $settings['landing_hero_subtitle'] ?? 'Learn from industry experts and take your career to the next level with our premium courses.' }}</textarea>
                </div>

                <div>
                    <label for="landing_cta_text" class="block text-sm font-medium text-gray-700 mb-1">CTA Button Text</label>
                    <input type="text" id="landing_cta_text" name="landing_cta_text" 
                           value="{{ $settings['landing_cta_text'] ?? 'Get Started Today' }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
            <h3 class="text-lg font-semibold mb-4 border-b border-gray-50 pb-2">Features Highlight</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 space-y-4">
                    <h4 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Feature 1</h4>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="landing_feature_1_title" 
                               value="{{ $settings['landing_feature_1_title'] ?? 'Expert Instructors' }}"
                               class="w-full px-3 py-2 rounded border border-gray-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="landing_feature_1_desc" rows="3"
                                  class="w-full px-3 py-2 rounded border border-gray-200 text-sm resize-none">{{ $settings['landing_feature_1_desc'] ?? 'Learn from the best in the industry.' }}</textarea>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 space-y-4">
                    <h4 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Feature 2</h4>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="landing_feature_2_title" 
                               value="{{ $settings['landing_feature_2_title'] ?? 'Flexible Learning' }}"
                               class="w-full px-3 py-2 rounded border border-gray-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="landing_feature_2_desc" rows="3"
                                  class="w-full px-3 py-2 rounded border border-gray-200 text-sm resize-none">{{ $settings['landing_feature_2_desc'] ?? 'Study at your own pace, anywhere, anytime.' }}</textarea>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 space-y-4">
                    <h4 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Feature 3</h4>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="landing_feature_3_title" 
                               value="{{ $settings['landing_feature_3_title'] ?? 'Career Support' }}"
                               class="w-full px-3 py-2 rounded border border-gray-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="landing_feature_3_desc" rows="3"
                                  class="w-full px-3 py-2 rounded border border-gray-200 text-sm resize-none">{{ $settings['landing_feature_3_desc'] ?? 'Get guidance to land your dream job.' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Action -->
    <div class="fixed bottom-[72px] md:bottom-0 md:static left-0 w-full md:w-auto p-4 md:p-0 bg-white md:bg-transparent border-t border-gray-200 md:border-t-0 z-40 md:mt-6">
        <button type="submit" class="w-full md:w-auto px-6 py-3.5 bg-primary text-white font-semibold rounded-xl shadow-sm hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Save Landing Page
        </button>
    </div>
</form>

<script>
    function toggleMode() {
        const mode = document.getElementById('landing_page_mode').value;
        const customHtmlSection = document.getElementById('custom_html_section');
        const defaultTemplateSections = document.getElementById('default_template_sections');
        
        if (mode === 'custom') {
            customHtmlSection.classList.remove('hidden');
            defaultTemplateSections.classList.add('hidden');
        } else {
            customHtmlSection.classList.add('hidden');
            defaultTemplateSections.classList.remove('hidden');
        }
    }
</script>
@endsection
