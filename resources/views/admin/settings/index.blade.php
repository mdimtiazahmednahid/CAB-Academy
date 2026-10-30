@extends('layouts.admin')

@section('header_title', 'Global Platform & Typography Settings')

@section('content')
<div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-[#051c45] tracking-tight">Global Platform Settings</h2>
        <p class="text-slate-500 mt-1 text-xs md:text-sm">Configure institutional identity, fluid typography, auto-responsive brand scaling, and contact parameters.</p>
    </div>
    <a href="{{ route('admin.frontend.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#051c45] hover:bg-[#0a2540] text-white text-xs font-bold shadow-xs transition-colors">
        <i class="fas fa-wand-magic-sparkles text-emerald-400"></i>
        <span>Open Frontend & Page CMS</span>
    </a>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 text-sm font-bold flex items-center gap-2">
        <i class="fas fa-circle-check text-emerald-600"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@php
    $initSiteName = $settings['site_name']->value ?? 'ACS Academy';
    $initShortName = $settings['site_short_name']->value ?? '';
    $initMinFont = (int) ($settings['ui_min_font_size']->value ?? 14);
    $initFontScale = $settings['ui_font_scale']->value ?? 'default';
    $initBrandMode = $settings['brand_name_size_mode']->value ?? 'auto';
@endphp

<form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8 max-w-4xl relative pb-24 md:pb-8"
      x-data="{
          previewSiteName: @js($initSiteName),
          previewShortName: @js($initShortName),
          minFontSize: {{ $initMinFont }},
          fontScale: @js($initFontScale),
          brandMode: @js($initBrandMode),
          get previewHeaderSize() {
              const len = (this.previewSiteName || '').length;
              if (this.brandMode === 'compact') return '13px';
              if (this.brandMode === 'large') return '20px';
              if (this.brandMode === 'standard') return '17px';
              if (len > 30) return '12.5px';
              if (len > 20) return '14.5px';
              return '18px';
          }
      }">
    @csrf

    <!-- 1. Branding & Institutional Identity Section -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 md:p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
            <div>
                <h3 class="text-base md:text-lg font-extrabold text-[#051c45]">Institutional Branding & Identity</h3>
                <p class="text-xs text-slate-500">Supports both compact acronyms (e.g., <span class="font-semibold text-slate-700">ACS Academy</span>) and full titles (e.g., <span class="font-semibold text-slate-700">Academy of Commercial and Supply Chain</span>).</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black uppercase tracking-wider">Auto-Adaptive</span>
        </div>
        
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Site Name -->
                <div class="md:col-span-2 focus-ring-primary rounded-lg">
                    <label for="site_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Institution / Site Name</label>
                    <input type="text" id="site_name" name="site_name" x-model="previewSiteName"
                           value="{{ $settings['site_name']->value ?? 'ACS Academy' }}"
                           placeholder="e.g. ACS Academy or Academy of Commercial and Supply Chain"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#051c45] transition-shadow text-sm font-bold text-[#051c45]">
                    <div class="flex flex-wrap items-center gap-1.5 mt-2">
                        <span class="text-[11px] text-slate-400">Quick test presets:</span>
                        <button type="button" @click="previewSiteName = 'ACS Academy'" class="text-[11px] px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors">ACS Academy</button>
                        <button type="button" @click="previewSiteName = 'ACS Academy'" class="text-[11px] px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors">ACS Academy</button>
                        <button type="button" @click="previewSiteName = 'Academy of Commercial and Supply Chain'" class="text-[11px] px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors">Academy of Commercial and Supply Chain</button>
                    </div>
                </div>

                <!-- Short Name (For Mobile App & PWA Icon Label) -->
                <div class="focus-ring-primary rounded-lg">
                    <label for="site_short_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Mobile / PWA Short Name</label>
                    <input type="text" id="site_short_name" name="site_short_name" x-model="previewShortName"
                           value="{{ $settings['site_short_name']->value ?? '' }}"
                           placeholder="Auto (e.g. ACS Academy)"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#051c45] transition-shadow text-sm font-semibold text-slate-800">
                    <p class="text-[11px] text-slate-400 mt-1">Used on mobile home screens & compact app bars.</p>
                </div>
            </div>

            <!-- Site Tagline / Slogan -->
            <div class="focus-ring-primary rounded-lg">
                <label for="site_tagline" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Site Tagline / Subtitle</label>
                <input type="text" id="site_tagline" name="site_tagline" 
                       value="{{ $settings['site_tagline']->value ?? 'Learn, Grow, Lead.' }}"
                       placeholder="e.g. Learn, Grow, Lead."
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#051c45] transition-shadow text-sm">
            </div>

            <!-- Logo Upload -->
            <div class="focus-ring-primary rounded-lg">
                <label for="logo_input" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Site Logo</label>
                @php
                    $currentLogo = \App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png');
                @endphp
                <div class="mb-3 p-3 bg-slate-50 rounded-xl inline-flex items-center gap-3 border border-slate-200/80">
                    <img id="current_logo_img" src="{{ $currentLogo }}" alt="Current Logo" class="h-12 w-auto object-contain" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                    <span class="text-xs text-slate-500 font-semibold">Active Logo Preview</span>
                </div>
                <input type="file" id="logo_input" name="logo" accept="image/*"
                       class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#c30102] hover:file:bg-red-100 transition-colors cursor-pointer">
                <input type="hidden" name="logo_base64" id="logo_base64">
                <script>
                    document.getElementById('logo_input')?.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(evt) {
                                document.getElementById('logo_base64').value = evt.target.result;
                                const preview = document.getElementById('current_logo_img');
                                if (preview) {
                                    preview.src = evt.target.result;
                                }
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                </script>
            </div>

            <!-- Primary Color -->
            <div class="focus-ring-primary rounded-lg">
                <label for="primary_color" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Primary Color (Hex)</label>
                <div class="flex items-center gap-3">
                    <input type="color" id="primary_color_picker" 
                           value="{{ $settings['primary_color']->value ?? '#c30102' }}"
                           oninput="document.getElementById('primary_color').value = this.value"
                           class="h-11 w-12 rounded-lg cursor-pointer border border-slate-200 p-0.5">
                    <input type="text" id="primary_color" name="primary_color" 
                           value="{{ $settings['primary_color']->value ?? '#c30102' }}"
                           oninput="document.getElementById('primary_color_picker').value = this.value"
                           class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:border-[#051c45] transition-shadow text-sm font-mono">
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Typography, Minimum Text Size & Auto-Responsive Brand Scaling Engine -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 md:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3 mb-5">
            <div>
                <h3 class="text-base md:text-lg font-extrabold text-[#051c45]">Typography, Minimum Text Size & Auto-Responsive Scaling</h3>
                <p class="text-xs text-slate-500">Set the global minimum font size and automatic brand title scaling across mobile, tablet, and desktop screens.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-[#051c45]/10 text-[#051c45] text-[10px] font-black uppercase tracking-wider">Fluid CSS Clamp Engine</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <!-- Minimum Base Font Size (px) -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                <div class="flex items-center justify-between mb-2">
                    <label for="ui_min_font_size" class="text-xs font-bold uppercase tracking-wider text-slate-700">Min Text Size (px)</label>
                    <span class="px-2 py-0.5 rounded bg-[#051c45] text-white text-xs font-black" x-text="minFontSize + 'px'"></span>
                </div>
                <input type="range" id="ui_min_font_size" name="ui_min_font_size" min="12" max="20" step="1"
                       x-model="minFontSize"
                       value="{{ $initMinFont }}"
                       class="w-full accent-[#c30102] cursor-pointer">
                <div class="flex justify-between text-[10px] text-slate-400 font-semibold mt-1">
                    <span>12px (Compact)</span>
                    <span>14px (Std)</span>
                    <span>20px (Large)</span>
                </div>
            </div>

            <!-- Font Scale Multiplier -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                <label for="ui_font_scale" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Global Text Scale</label>
                <select id="ui_font_scale" name="ui_font_scale" x-model="fontScale"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-[#051c45] focus:outline-none focus:border-[#051c45]">
                    <option value="compact">Compact Density (0.94x)</option>
                    <option value="default">Default Balanced (1.0x)</option>
                    <option value="comfortable">Comfortable Reading (1.06x)</option>
                    <option value="large">High Accessibility (1.12x)</option>
                </select>
                <p class="text-[11px] text-slate-500 mt-1.5">Scales all headings, cards, tables, and buttons proportionally.</p>
            </div>

            <!-- Brand Name Sizing Mode -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                <label for="brand_name_size_mode" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Brand Name Sizing Mode</label>
                <select id="brand_name_size_mode" name="brand_name_size_mode" x-model="brandMode"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-[#051c45] focus:outline-none focus:border-[#051c45]">
                    <option value="auto">Auto-Fit (Smart Character Length Scaling)</option>
                    <option value="compact">Compact Header Lockup</option>
                    <option value="standard">Standard Brand Lockup</option>
                    <option value="large">Prominent Executive Lockup</option>
                </select>
                <p class="text-[11px] text-slate-500 mt-1.5">Auto-Fit automatically shrinks long names (>22 chars) so they never overflow.</p>
            </div>
        </div>

        <!-- Live Auto-Responsive Brand & Typography Preview -->
        <div class="rounded-2xl border border-slate-200 bg-[#f8fafc] p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Live Auto-Responsive Header & Mobile Preview</span>
                <span class="text-[11px] font-semibold text-emerald-700" x-text="(previewSiteName || '').length > 22 ? 'Long Brand Name Detected (' + (previewSiteName || '').length + ' chars) — Auto-Scaling Active' : 'Short Brand Name (' + (previewSiteName || '').length + ' chars) — Standard Lockup'"></span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Simulated Public Navbar Lockup -->
                <div class="bg-white rounded-xl border border-slate-200 p-3.5 flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2.5 max-w-[250px]">
                        <img src="{{ $currentLogo }}" alt="Logo" class="w-9 h-9 rounded-full object-contain border border-slate-100 shrink-0" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                        <div class="min-w-0">
                            <div class="font-black uppercase text-[#051c45] leading-tight" :style="'font-size: ' + previewHeaderSize" x-html="(previewSiteName || 'ACS Academy').replace(/^(academy\s+of)\s+/i, '$1<br>')"></div>
                            <div class="text-[8px] font-black uppercase tracking-widest text-[#c30102] mt-0.5">Learn, Grow, Lead.</div>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-[#c30102] text-white text-[10px] font-bold shrink-0">Sign In</span>
                </div>

                <!-- Simulated Dark Executive Sidebar Lockup -->
                <div class="bg-[#03112b] rounded-xl border border-slate-800 p-3.5 flex items-center justify-between text-white">
                    <div class="flex items-center gap-2.5 max-w-[240px]">
                        <img src="{{ $currentLogo }}" alt="Logo" class="w-8 h-8 rounded-lg bg-white p-0.5 object-contain shrink-0" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                        <div class="min-w-0">
                            <div class="font-black uppercase text-white leading-tight" :style="'font-size: ' + previewHeaderSize" x-html="(previewSiteName || 'ACS Academy').replace(/^(academy\s+of)\s+/i, '$1<br>')"></div>
                            <div class="text-[9px] font-bold uppercase tracking-widest text-emerald-400">Executive OS</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400" x-text="'Min ' + minFontSize + 'px'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact & SEO Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h3 class="text-lg font-semibold mb-4 border-b border-gray-50 pb-2">Contact & SEO</h3>
        
        <div class="space-y-5">
            <!-- Campus Address -->
            <div class="focus-ring-primary rounded-lg">
                <label for="contact_address" class="block text-sm font-medium text-gray-700 mb-1">Campus Address</label>
                <input type="text" id="contact_address" name="contact_address" 
                       value="{{ $settings['contact_address']->value ?? 'Mirpur-Pallabi, Dhaka, Bangladesh, 1216' }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
            </div>

            <!-- Executive Hotline -->
            <div class="focus-ring-primary rounded-lg">
                <label for="contact_hotline" class="block text-sm font-medium text-gray-700 mb-1">Executive Hotline</label>
                <input type="text" id="contact_hotline" name="contact_hotline" 
                       value="{{ $settings['contact_hotline']->value ?? '01917-054664' }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
            </div>

            <!-- WhatsApp Number -->
            <div class="focus-ring-primary rounded-lg">
                <label for="contact_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Hotline</label>
                <input type="text" id="contact_whatsapp" name="contact_whatsapp" 
                       value="{{ $settings['contact_whatsapp']->value ?? '8801917054664' }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
            </div>

            <!-- Global WhatsApp Inquiry Message Template -->
            <div class="focus-ring-primary rounded-lg">
                <label for="whatsapp_inquiry_template" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Counselor Message Template</label>
                <textarea id="whatsapp_inquiry_template" name="whatsapp_inquiry_template" rows="2"
                          placeholder="Assalamu Alaikum! I would like to inquire/enroll in *{title}* (Fee: {price}). Please share batch details and enrollment procedure: {url}"
                          class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-sm resize-none">{{ $settings['whatsapp_inquiry_template']->value ?? 'Assalamu Alaikum! I would like to inquire/enroll in *{title}* (Fee: {price}). Please share batch details and enrollment procedure: {url}' }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Available variables: <code>{title}</code>, <code>{price}</code>, <code>{batch}</code>, <code>{url}</code>. Can also be overridden per course.</p>
            </div>

            <!-- Contact Email -->
            <div class="focus-ring-primary rounded-lg">
                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Support Email</label>
                <input type="email" id="contact_email" name="contact_email" 
                       value="{{ $settings['contact_email']->value ?? 'commercialacademy868@gmail.com' }}"
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

    <!-- Landing Page Customization Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 md:p-6">
        <h3 class="text-lg font-semibold mb-4 border-b border-gray-50 pb-2">Landing Page Customization</h3>
        
        <div class="space-y-5">
            <!-- Under Construction Mode -->
            <div class="flex items-center gap-3 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                <input type="hidden" name="under_construction_mode" value="0">
                <input type="checkbox" id="under_construction_mode" name="under_construction_mode" value="1" 
                       {{ (isset($settings['under_construction_mode']) && $settings['under_construction_mode']->value == '1') ? 'checked' : '' }}
                       class="w-5 h-5 text-yellow-600 border-yellow-300 rounded focus:ring-yellow-500">
                <label for="under_construction_mode" class="text-sm font-medium text-yellow-900">
                    Enable "Under Construction" Banner Mode
                </label>
            </div>

            <!-- Hero Section Texts -->
            <h4 class="font-medium text-gray-800 pt-2">Hero Section</h4>
            
            <div class="focus-ring-primary rounded-lg">
                <label for="landing_hero_title" class="block text-sm font-medium text-gray-700 mb-1">Hero Title</label>
                <input type="text" id="landing_hero_title" name="landing_hero_title" 
                       value="{{ $settings['landing_hero_title']->value ?? 'Master Your Future with ACS Academy' }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
            </div>

            <div class="focus-ring-primary rounded-lg">
                <label for="landing_hero_subtitle" class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle</label>
                <textarea id="landing_hero_subtitle" name="landing_hero_subtitle" rows="2"
                          class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base resize-none">{{ $settings['landing_hero_subtitle']->value ?? 'Join thousands of students learning in-demand skills from industry experts. Start your journey today and transform your career.' }}</textarea>
            </div>
            
            <div class="focus-ring-primary rounded-lg">
                <label for="landing_hero_cta" class="block text-sm font-medium text-gray-700 mb-1">Hero Button Text</label>
                <input type="text" id="landing_hero_cta" name="landing_hero_cta" 
                       value="{{ $settings['landing_hero_cta']->value ?? 'Explore Courses' }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
            </div>

            <!-- CTA Section Texts -->
            <h4 class="font-medium text-gray-800 pt-4 border-t border-gray-50">Bottom CTA Section</h4>
            
            <div class="focus-ring-primary rounded-lg">
                <label for="landing_cta_title" class="block text-sm font-medium text-gray-700 mb-1">CTA Title</label>
                <input type="text" id="landing_cta_title" name="landing_cta_title" 
                       value="{{ $settings['landing_cta_title']->value ?? 'Ready to start learning?' }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
            </div>

            <div class="focus-ring-primary rounded-lg">
                <label for="landing_cta_subtitle" class="block text-sm font-medium text-gray-700 mb-1">CTA Subtitle</label>
                <textarea id="landing_cta_subtitle" name="landing_cta_subtitle" rows="2"
                          class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base resize-none">{{ $settings['landing_cta_subtitle']->value ?? 'Join our community today and get access to all premium features.' }}</textarea>
            </div>
            
            <div class="focus-ring-primary rounded-lg">
                <label for="landing_cta_button" class="block text-sm font-medium text-gray-700 mb-1">CTA Button Text</label>
                <input type="text" id="landing_cta_button" name="landing_cta_button" 
                       value="{{ $settings['landing_cta_button']->value ?? 'Get Started for Free' }}"
                       class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:border-transparent transition-shadow text-base">
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
