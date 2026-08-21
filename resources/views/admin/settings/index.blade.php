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

    <!-- Registration Journey Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6" x-data="registrationFields()">
        <h3 class="text-lg font-semibold mb-1 border-b border-gray-50 pb-2 flex justify-between items-center">
            Registration Journey (Onboarding)
            <button type="button" @click="addField" class="text-sm px-3 py-1 bg-primary/10 text-primary rounded-lg hover:bg-primary/20 transition-colors font-medium">
                + Add Field
            </button>
        </h3>
        <p class="text-sm text-gray-500 mb-4">Add custom fields for users to fill out during registration (e.g. Industry, Goals).</p>
        
        <input type="hidden" name="has_registration_fields" value="1">
        
        <div class="space-y-4">
            <template x-for="(field, index) in fields" :key="index">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 relative group">
                    <button type="button" @click="removeField(index)" class="absolute top-3 right-3 text-red-400 hover:text-red-600 bg-white rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity" title="Remove field">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Field Name (Internal)</label>
                            <input type="text" x-model="field.name" :name="`registration_fields[${index}][name]`" placeholder="e.g. industry" required class="w-full text-sm px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-primary transition-shadow">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Display Label</label>
                            <input type="text" x-model="field.label" :name="`registration_fields[${index}][label]`" placeholder="e.g. What industry are you in?" required class="w-full text-sm px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-primary transition-shadow">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Field Type</label>
                            <select x-model="field.type" :name="`registration_fields[${index}][type]`" class="w-full text-sm px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-primary transition-shadow">
                                <option value="text">Text Input</option>
                                <option value="select">Dropdown (Select)</option>
                            </select>
                        </div>
                        <div x-show="field.type === 'select'">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Options (comma separated)</label>
                            <input type="text" x-model="field.options" :name="`registration_fields[${index}][options]`" placeholder="Technology, Finance, Other" class="w-full text-sm px-3 py-2 rounded-lg border border-gray-200 focus:outline-none focus:border-primary transition-shadow">
                        </div>
                    </div>
                    <div class="mt-3 flex items-center">
                        <input type="hidden" :name="`registration_fields[${index}][is_mandatory]`" value="0">
                        <input type="checkbox" x-model="field.is_mandatory" :name="`registration_fields[${index}][is_mandatory]`" value="1" class="rounded border-gray-300 text-primary focus:ring-primary w-4 h-4">
                        <label class="ml-2 text-sm text-gray-700">Make this field mandatory</label>
                    </div>
                </div>
            </template>
            
            <div x-show="fields.length === 0" class="text-center py-6 text-gray-500 text-sm border-2 border-dashed border-gray-200 rounded-xl" style="display: none;">
                No registration fields configured. Click "Add Field" to create one.
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

@php
    $defaultRegistrationFields = [
        ['name' => 'job_role', 'label' => 'Current Job Role', 'type' => 'select', 'options' => 'Developer, Manager, Designer, Marketing, Other', 'is_mandatory' => true],
        ['name' => 'industry', 'label' => 'Industry', 'type' => 'select', 'options' => 'Technology, Finance, Healthcare, Retail, Education', 'is_mandatory' => false],
        ['name' => 'primary_goal', 'label' => 'Primary Goal', 'type' => 'select', 'options' => 'Upskill, Career Change, Team Training', 'is_mandatory' => true]
    ];
    
    $savedRegistrationFields = isset($settings['registration_fields']) ? json_decode($settings['registration_fields']->value, true) : null;
    $registrationFieldsJson = json_encode($savedRegistrationFields ?? $defaultRegistrationFields);
@endphp
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('registrationFields', () => ({
            fields: {!! $registrationFieldsJson !!} || [],
            addField() {
                this.fields.push({
                    name: '',
                    label: '',
                    type: 'text',
                    options: '',
                    is_mandatory: false
                });
            },
            removeField(index) {
                this.fields.splice(index, 1);
            }
        }));
    });
</script>
@endsection
