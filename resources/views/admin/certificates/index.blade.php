@extends('layouts.admin')

@section('header_title', 'Certificates & Template Customizer')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-1">Certificates & Template Customizer</h1>
        <p class="text-gray-500 text-xs sm:text-sm">Customize institution name (default: ACS Academy), wording, signatures, and design templates.</p>
    </div>
    <div class="flex items-center gap-2.5">
        <a href="{{ route('admin.certificates.preview') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 hover:text-primary hover:border-primary rounded-xl font-bold text-xs sm:text-sm shadow-xs hover:shadow-md transition-all">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            <span>Full-Page Print Preview</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2.5">
        <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Two-Column Layout: Customizer Form (Left) & Real-Time Live Preview (Right) -->
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start mb-12">
    
    <!-- Left Column: Certificate Settings & Template Selector (7 Cols) -->
    <div class="xl:col-span-7 bg-white rounded-2xl sm:rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 p-5 sm:p-7">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100">
            <div>
                <h2 class="text-base sm:text-lg font-black text-gray-900">Certificate Brand & Content Settings</h2>
                <p class="text-xs text-gray-500 mt-0.5">Control institution wording and select from 4 luxury certificate templates.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                Live Engine
            </span>
        </div>

        <form method="POST" action="{{ route('admin.certificates.settings.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <input type="hidden" name="customizer_toggles_present" value="1">

            <!-- 1. Design Template Selector -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                    Select Certificate Design Template <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    
                    <!-- Template 0 (Default): Official ACS Signature Certificate -->
                    <label class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all {{ $settings['template'] === 'acs_signature' ? 'border-[#b91c1c] bg-[#b91c1c]/5 shadow-sm' : 'border-gray-200 hover:border-gray-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-900">ACS Official Signature</span>
                            <input type="radio" name="certificate_template" value="acs_signature" {{ $settings['template'] === 'acs_signature' ? 'checked' : '' }} class="text-[#b91c1c] focus:ring-[#b91c1c]">
                        </div>
                        <div class="h-10 rounded-lg border-2 border-[#b91c1c] bg-[#fcf8f6] flex items-center justify-center text-[10px] font-sans text-[#5b1415] font-black tracking-wider uppercase">
                            Official ACS Default
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2">Red border, gold-rimmed ACS crest, watermark emblem, and scalloped gold seal.</p>
                    </label>

                    <!-- Template 1: Classic Royal Navy & Gold -->
                    <label class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all {{ $settings['template'] === 'classic_navy' ? 'border-[#063b96] bg-[#063b96]/5 shadow-sm' : 'border-gray-200 hover:border-gray-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-900">Royal Navy & Gold</span>
                            <input type="radio" name="certificate_template" value="classic_navy" {{ $settings['template'] === 'classic_navy' ? 'checked' : '' }} class="text-[#063b96] focus:ring-[#063b96]">
                        </div>
                        <div class="h-10 rounded-lg border-2 border-[#b89758] bg-[#063b96] flex items-center justify-center text-[10px] font-serif text-[#f4e2be] font-bold tracking-widest uppercase">
                            Classic Prestige
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2">Imperial Navy outer border with ornate gold guilloche and classic crest.</p>
                    </label>

                    <!-- Template 2: Modern Luxury Emerald -->
                    <label class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all {{ $settings['template'] === 'modern_emerald' ? 'border-[#064e3b] bg-[#064e3b]/5 shadow-sm' : 'border-gray-200 hover:border-gray-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-900">Modern Luxury Emerald</span>
                            <input type="radio" name="certificate_template" value="modern_emerald" {{ $settings['template'] === 'modern_emerald' ? 'checked' : '' }} class="text-[#064e3b] focus:ring-[#064e3b]">
                        </div>
                        <div class="h-10 rounded-lg border-2 border-[#10b981] bg-[#064e3b] flex items-center justify-center text-[10px] font-sans text-[#a7f3d0] font-bold tracking-widest uppercase">
                            Verification Emerald
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2">Deep forest emerald with crisp platinum accents and geometric security lines.</p>
                    </label>

                    <!-- Template 3: Crimson Prestige -->
                    <label class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all {{ $settings['template'] === 'crimson_prestige' ? 'border-[#880102] bg-[#880102]/5 shadow-sm' : 'border-gray-200 hover:border-gray-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-900">Crimson Prestige</span>
                            <input type="radio" name="certificate_template" value="crimson_prestige" {{ $settings['template'] === 'crimson_prestige' ? 'checked' : '' }} class="text-[#880102] focus:ring-[#880102]">
                        </div>
                        <div class="h-10 rounded-lg border-2 border-[#063b96] bg-[#880102] flex items-center justify-center text-[10px] font-serif text-[#ffe4e6] font-bold tracking-widest uppercase">
                            Regal Crimson
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2">ACS conversion crimson border with obsidian navy and gilded gold stamp.</p>
                    </label>

                    <!-- Template 4: Minimalist Corporate -->
                    <label class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all {{ $settings['template'] === 'minimalist_corporate' ? 'border-slate-800 bg-slate-100 shadow-sm' : 'border-gray-200 hover:border-gray-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-xs text-gray-900">Minimalist Corporate</span>
                            <input type="radio" name="certificate_template" value="minimalist_corporate" {{ $settings['template'] === 'minimalist_corporate' ? 'checked' : '' }} class="text-slate-800 focus:ring-slate-800">
                        </div>
                        <div class="h-10 rounded-lg border border-slate-300 bg-slate-900 flex items-center justify-center text-[10px] font-sans text-slate-100 font-bold tracking-widest uppercase">
                            Swiss Modern
                        </div>
                        <p class="text-[10px] text-gray-500 mt-2">Clean, stark, Swiss-modern executive certificate layout with high contrast.</p>
                    </label>

                </div>
            </div>

            <!-- 2. Institution Name (The Core Option Requested by User) -->
            <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/70 space-y-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-amber-900 mb-1 flex items-center justify-between">
                        <span>Institution Name on Certificates <span class="text-red-500">*</span></span>
                        <span class="text-[10px] text-amber-700 font-semibold">(Default: ACS Academy)</span>
                    </label>
                    <input 
                        type="text" 
                        name="certificate_institution_name" 
                        id="form_inst_name"
                        value="{{ old('certificate_institution_name', $settings['institution_name']) }}" 
                        placeholder="ACS Academy"
                        required 
                        class="w-full text-xs sm:text-sm font-bold rounded-xl border-amber-300 bg-white focus:border-[#063b96] focus:ring-[#063b96] py-2.5 px-3 shadow-xs"
                    >
                    <p class="text-[11px] text-amber-800/80 mt-1">
                        You can keep it as <strong>ACS Academy</strong> or customize it to <strong>ACS Academy</strong> or any corporate entity name whenever you prefer.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tagline / Sub-Header</label>
                        <input 
                            type="text" 
                            name="certificate_tagline" 
                            id="form_tagline"
                            value="{{ old('certificate_tagline', $settings['tagline']) }}" 
                            placeholder="ACS Academy • Learn, Grow, Lead."
                            class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Campus / Location Text</label>
                        <input 
                            type="text" 
                            name="certificate_campus_address" 
                            id="form_campus"
                            value="{{ old('certificate_campus_address', $settings['campus_address']) }}" 
                            placeholder="Mirpur-Pallabi Campus • Dhaka, Bangladesh"
                            class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3"
                        >
                    </div>
                </div>
            </div>

            <!-- 3. Titles & Conferred Upon Wording -->
            <div class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Certificate Honor Title <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="certificate_title" 
                            id="form_cert_title"
                            value="{{ old('certificate_title', $settings['title']) }}" 
                            placeholder="Executive Certification of Mastery"
                            required 
                            class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Conferred Upon Intro <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            name="certificate_conferment_text" 
                            id="form_confer_text"
                            value="{{ old('certificate_conferment_text', $settings['conferment_text']) }}" 
                            placeholder="This is proudly presented and conferred upon"
                            required 
                            class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Completion Description Text <span class="text-red-500">*</span></label>
                    <textarea 
                        name="certificate_completion_text" 
                        id="form_completion_text"
                        rows="2" 
                        required 
                        class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] p-3 leading-relaxed"
                    >{{ old('certificate_completion_text', $settings['completion_text']) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Faculty Endorsement / Auspices Line</label>
                    <input 
                        type="text" 
                        name="certificate_faculty_endorsement" 
                        id="form_faculty"
                        value="{{ old('certificate_faculty_endorsement', $settings['faculty_endorsement']) }}" 
                        placeholder="Delivered under the auspices of the ACS Academy Executive Faculty Board."
                        class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3"
                    >
                </div>
            </div>

            <!-- 4. Signatures & Official Seal Customization -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3.5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Primary Signatory Name</label>
                        <input 
                            type="text" 
                            name="certificate_primary_signatory_name" 
                            id="form_sig1_name"
                            value="{{ old('certificate_primary_signatory_name', $settings['primary_signatory_name']) }}" 
                            required 
                            class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 bg-white"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Primary Signatory Title</label>
                        <input 
                            type="text" 
                            name="certificate_primary_signatory_title" 
                            id="form_sig1_title"
                            value="{{ old('certificate_primary_signatory_title', $settings['primary_signatory_title']) }}" 
                            required 
                            class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 bg-white"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Signatory Board / Council</label>
                        <input 
                            type="text" 
                            name="certificate_primary_signatory_board" 
                            id="form_sig1_board"
                            value="{{ old('certificate_primary_signatory_board', $settings['primary_signatory_board']) }}" 
                            class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 bg-white"
                        >
                    </div>
                </div>

                <!-- Secondary Signatory (Optional) -->
                <div class="pt-2 border-t border-slate-200/80">
                    <div class="flex items-center mb-2">
                        <input 
                            type="checkbox" 
                            name="certificate_show_secondary_signature" 
                            id="certificate_show_secondary_signature" 
                            value="1" 
                            {{ !empty($settings['show_secondary_signature']) && $settings['show_secondary_signature'] == '1' ? 'checked' : '' }}
                            class="rounded text-primary focus:ring-primary mr-2 cursor-pointer"
                        >
                        <label for="certificate_show_secondary_signature" class="text-xs font-bold text-gray-800 cursor-pointer">
                            Enable Dual Signature (Lead Executive Trainer / Faculty Chair)
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Second Signatory Name</label>
                            <input 
                                type="text" 
                                name="certificate_secondary_signatory_name" 
                                value="{{ old('certificate_secondary_signatory_name', $settings['secondary_signatory_name']) }}" 
                                placeholder="Lead Executive Trainer"
                                class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 bg-white"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Second Signatory Designation</label>
                            <input 
                                type="text" 
                                name="certificate_secondary_signatory_title" 
                                value="{{ old('certificate_secondary_signatory_title', $settings['secondary_signatory_title']) }}" 
                                placeholder="Course Director & Faculty Chair"
                                class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 bg-white"
                            >
                        </div>
                    </div>

                    <!-- Signature Image Uploads (Transparent PNG/SVG Recommended) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div class="bg-white p-3 rounded-xl border border-slate-200/80 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <label class="block text-xs font-bold text-gray-700">Lead Trainer Signature</label>
                                <img src="{{ asset(ltrim($settings['trainer_signature_url'] ?? 'images/cert-official/sig-trainer.svg', '/')) }}"
                                     alt="Lead Trainer Signature"
                                     class="h-7 w-auto object-contain bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200">
                            </div>
                            <input type="file"
                                   name="certificate_trainer_signature_file"
                                   accept=".png,.svg,.jpg,.jpeg,.webp,image/png,image/svg+xml"
                                   class="block w-full text-xs text-gray-600 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[11px] text-slate-500 leading-snug">
                                Recommended: <strong>600 × 240 px</strong> (5:2 ratio), <strong>transparent PNG or SVG</strong> (max 2 MB). Transparent instructor signature strongly recommended.
                            </p>
                        </div>

                        <div class="bg-white p-3 rounded-xl border border-slate-200/80 space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <label class="block text-xs font-bold text-gray-700">ACS Governing Body Signature</label>
                                <img src="{{ asset(ltrim($settings['governing_signature_url'] ?? 'images/cert-official/sig-governing.svg', '/')) }}"
                                     alt="ACS Governing Body Signature"
                                     class="h-7 w-auto object-contain bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200">
                            </div>
                            <input type="file"
                                   name="certificate_governing_signature_file"
                                   accept=".png,.svg,.jpg,.jpeg,.webp,image/png,image/svg+xml"
                                   class="block w-full text-xs text-gray-600 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-[11px] text-slate-500 leading-snug">
                                Recommended: <strong>600 × 240 px</strong> (5:2 ratio), <strong>transparent PNG or SVG</strong> (max 2 MB). Transparent background strongly recommended.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Official Gold Seal, Overlay Text, Seal Logo & Verification QR Controls -->
                <div class="pt-3 border-t border-slate-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black uppercase tracking-wider text-amber-900">
                            🏅 Official Seal, Overlay Text, Logo &amp; Verification QR Controls
                        </span>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            Full Admin Control
                        </span>
                    </div>

                    <!-- Visibility Toggles Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 bg-white p-3 rounded-xl border border-slate-200/80">
                        <label class="flex items-center gap-2 text-xs font-bold text-gray-800 cursor-pointer">
                            <input type="checkbox" name="certificate_show_seal" id="form_show_seal" value="1"
                                {{ ($settings['show_seal'] ?? '1') == '1' ? 'checked' : '' }}
                                class="rounded text-[#063b96] focus:ring-[#063b96] cursor-pointer">
                            <span>Show Official Gold Seal</span>
                        </label>

                        <label class="flex items-center gap-2 text-xs font-bold text-gray-800 cursor-pointer">
                            <input type="checkbox" name="certificate_show_seal_logo" id="form_show_seal_logo" value="1"
                                {{ ($settings['show_seal_logo'] ?? '1') == '1' ? 'checked' : '' }}
                                class="rounded text-[#063b96] focus:ring-[#063b96] cursor-pointer">
                            <span>Show Logo Inside Seal</span>
                        </label>

                        <label class="flex items-center gap-2 text-xs font-bold text-gray-800 cursor-pointer">
                            <input type="checkbox" name="certificate_show_seal_text" id="form_show_seal_text" value="1"
                                {{ ($settings['show_seal_text'] ?? '1') == '1' ? 'checked' : '' }}
                                class="rounded text-[#063b96] focus:ring-[#063b96] cursor-pointer">
                            <span>Show Curved Seal Text</span>
                        </label>

                        <label class="flex items-center gap-2 text-xs font-bold text-gray-800 cursor-pointer">
                            <input type="checkbox" name="certificate_show_qr_code" id="form_show_qr_code" value="1"
                                {{ ($settings['show_qr_code'] ?? '1') == '1' ? 'checked' : '' }}
                                class="rounded text-[#063b96] focus:ring-[#063b96] cursor-pointer">
                            <span>Show Verification QR Code</span>
                        </label>

                        <label class="flex items-center gap-2 text-xs font-bold text-gray-800 cursor-pointer">
                            <input type="checkbox" name="certificate_show_watermark" id="form_show_watermark" value="1"
                                {{ ($settings['show_watermark'] ?? '1') == '1' ? 'checked' : '' }}
                                class="rounded text-[#063b96] focus:ring-[#063b96] cursor-pointer">
                            <span>Show Center Logo Watermark</span>
                        </label>
                    </div>

                    <!-- Curved Seal Overlay Text -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Official Security Seal Stamp Text (Curved Inside Gold Seal)</label>
                        <input 
                            type="text" 
                            name="certificate_seal_text" 
                            id="form_seal_text"
                            value="{{ old('certificate_seal_text', $settings['seal_text']) }}" 
                            placeholder="OFFICIAL SEAL • VERIFIED"
                            class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 bg-white"
                        >
                        <p class="text-[11px] text-gray-500 mt-1">
                            Tip: Use <strong>•</strong> to split Top &amp; Bottom curved seal text (e.g. <code>OFFICIAL SEAL • VERIFIED</code> or <code>ACS ACADEMY • CERTIFIED</code>) around the center ACS logo inside the seal.
                        </p>
                    </div>

                    <!-- Dynamic Footer Labels (Certificate ID, Date, Batch) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Certificate ID Prefix Label</label>
                            <input 
                                type="text" 
                                name="certificate_id_label" 
                                id="form_id_label"
                                value="{{ old('certificate_id_label', $settings['id_label'] ?? 'CERTIFICATE ID:') }}" 
                                placeholder="CERTIFICATE ID:"
                                class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 bg-white"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Date Prefix Label</label>
                            <input 
                                type="text" 
                                name="certificate_date_label" 
                                id="form_date_label"
                                value="{{ old('certificate_date_label', $settings['date_label'] ?? 'DATE:') }}" 
                                placeholder="DATE:"
                                class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 bg-white"
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Batch Prefix Label</label>
                            <input 
                                type="text" 
                                name="certificate_batch_label" 
                                id="form_batch_label"
                                value="{{ old('certificate_batch_label', $settings['batch_label'] ?? 'BATCH:') }}" 
                                placeholder="BATCH:"
                                class="w-full text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 bg-white"
                            >
                        </div>
                    </div>

                    <!-- Custom Header Logo, Seal Medallion Logo & Gold Seal Base Upload / URL (Default: Latest Gold ACS Assets) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="bg-white p-3 rounded-xl border border-slate-200/80 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-gray-700">Header &amp; Watermark Logo</label>
                                <img src="{{ asset(ltrim($settings['logo_url'] ?? '/images/certificate-logo-gold.png', '/')) }}" class="w-6 h-6 object-contain" alt="Current Header Logo">
                            </div>
                            <input 
                                type="file" 
                                name="certificate_logo_file" 
                                id="form_logo_file"
                                accept="image/*"
                                class="block w-full text-[10px] text-gray-600 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200"
                            >
                            <input 
                                type="text" 
                                name="certificate_logo_url" 
                                id="form_logo_url"
                                value="{{ old('certificate_logo_url', $settings['logo_url'] ?? '/images/logo.png') }}" 
                                placeholder="/images/certificate-logo-gold.png"
                                class="w-full text-[10px] rounded-lg border-gray-200 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2"
                            >
                        </div>

                        <div class="bg-white p-3 rounded-xl border border-slate-200/80 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-gray-700">Inner Seal Logo</label>
                                <img src="{{ asset(ltrim($settings['seal_logo_url'] ?? '/images/certificate-logo-gold.png', '/')) }}" class="w-6 h-6 object-contain" alt="Current Seal Logo">
                            </div>
                            <input 
                                type="file" 
                                name="certificate_seal_logo_file" 
                                id="form_seal_logo_file"
                                accept="image/*"
                                class="block w-full text-[10px] text-gray-600 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-amber-50 file:text-amber-900 hover:file:bg-amber-100"
                            >
                            <input 
                                type="text" 
                                name="certificate_seal_logo_url" 
                                id="form_seal_logo_url"
                                value="{{ old('certificate_seal_logo_url', $settings['seal_logo_url'] ?? '/images/certificate-logo-gold.png') }}" 
                                placeholder="/images/certificate-logo-gold.png"
                                class="w-full text-[10px] rounded-lg border-gray-200 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2"
                            >
                        </div>

                        <div class="bg-white p-3 rounded-xl border border-slate-200/80 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-gray-700">Scalloped Gold Seal Base</label>
                                <img src="{{ asset(ltrim($settings['seal_bg_url'] ?? '/images/certificate-gold-seal.png', '/')) }}" class="w-6 h-6 object-contain" alt="Current Gold Seal">
                            </div>
                            <input 
                                type="file" 
                                name="certificate_seal_bg_file" 
                                id="form_seal_bg_file"
                                accept="image/*"
                                class="block w-full text-[10px] text-gray-600 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-amber-50 file:text-amber-900 hover:file:bg-amber-100"
                            >
                            <input 
                                type="text" 
                                name="certificate_seal_bg_url" 
                                id="form_seal_bg_url"
                                value="{{ old('certificate_seal_bg_url', $settings['seal_bg_url'] ?? '/images/certificate-gold-seal.png') }}" 
                                placeholder="/images/certificate-gold-seal.png"
                                class="w-full text-[10px] rounded-lg border-gray-200 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2"
                            >
                        </div>
                    </div>

                    <!-- Typography, Font Families & Sizes (px) -->
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-[#063b96]">Typography, Font Families &amp; Sizes</span>
                            <button type="button" id="reset_font_sizes_btn" class="text-[11px] font-bold text-[#063b96] hover:underline cursor-pointer">
                                Reset Defaults
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Title / Heading Font</label>
                                <select name="certificate_title_font" id="form_title_font" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                                    @foreach(['Cinzel' => 'Cinzel (Classic Engraved Serif)', 'Cinzel Decorative' => 'Cinzel Decorative (Royal Crest)', 'Playfair Display' => 'Playfair Display (Luxury Serif)', 'Cormorant Garamond' => 'Cormorant Garamond (Heritage Serif)', 'EB Garamond' => 'EB Garamond (Classic Academic)', 'Libre Baskerville' => 'Libre Baskerville (Traditional Serif)', 'Merriweather' => 'Merriweather (Formal Serif)', 'Montserrat' => 'Montserrat (Modern Executive Sans)', 'Plus Jakarta Sans' => 'Plus Jakarta Sans (Clean Sans)', 'Poppins' => 'Poppins (Geometric Sans)'] as $fontKey => $fontLabel)
                                        <option value="{{ $fontKey }}" {{ old('certificate_title_font', $settings['title_font'] ?? 'Cinzel') === $fontKey ? 'selected' : '' }}>{{ $fontLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Recipient Name Font</label>
                                <select name="certificate_recipient_font" id="form_recipient_font" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                                    @foreach(['Alex Brush' => 'Alex Brush (Signature Script)', 'Great Vibes' => 'Great Vibes (Formal Calligraphy)', 'Allura' => 'Allura (Flowing Script)', 'Pinyon Script' => 'Pinyon Script (Royal Crest Script)', 'Parisienne' => 'Parisienne (Classic Parisian Script)', 'Tangerine' => 'Tangerine (Tall Calligraphy)', 'Dancing Script' => 'Dancing Script (Expressive Script)', 'Sacramento' => 'Sacramento (Minimalist Script)', 'Playfair Display' => 'Playfair Display (Executive Serif)', 'Cinzel' => 'Cinzel (Imperial Capitals)', 'Cormorant Garamond' => 'Cormorant Garamond (Refined Serif)', 'EB Garamond' => 'EB Garamond (Academic Serif)'] as $fontKey => $fontLabel)
                                        <option value="{{ $fontKey }}" {{ old('certificate_recipient_font', $settings['recipient_font'] ?? 'Alex Brush') === $fontKey ? 'selected' : '' }}>{{ $fontLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Course Title Font</label>
                                <select name="certificate_course_font" id="form_course_font" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                                    @foreach(['Anton' => 'Anton (Bold Display)', 'Bebas Neue' => 'Bebas Neue (Tall Impact Caps)', 'Oswald' => 'Oswald (Condensed Display)', 'Cinzel' => 'Cinzel (Executive Serif)', 'Playfair Display' => 'Playfair Display (Luxury Serif)', 'Montserrat' => 'Montserrat (Bold Geometric Sans)', 'Poppins' => 'Poppins (Modern Bold Sans)', 'Lora' => 'Lora (Classic Serif)'] as $fontKey => $fontLabel)
                                        <option value="{{ $fontKey }}" {{ old('certificate_course_font', $settings['course_font'] ?? 'Anton') === $fontKey ? 'selected' : '' }}>{{ $fontLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Body / Conferment Font</label>
                                <select name="certificate_body_font" id="form_body_font" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                                    @foreach(['Aleo' => 'Aleo (Balanced Slab Serif)', 'Lora' => 'Lora (Warm Editorial Serif)', 'Merriweather' => 'Merriweather (Academic Serif)', 'EB Garamond' => 'EB Garamond (Classic Garamond)', 'Libre Baskerville' => 'Libre Baskerville (Book Serif)', 'Montserrat' => 'Montserrat (Crisp Sans)', 'Plus Jakarta Sans' => 'Plus Jakarta Sans (Modern Sans)', 'Poppins' => 'Poppins (Clean Geometric)', 'Roboto' => 'Roboto (Standard Sans)', 'Open Sans' => 'Open Sans (Clear Body Sans)'] as $fontKey => $fontLabel)
                                        <option value="{{ $fontKey }}" {{ old('certificate_body_font', $settings['body_font'] ?? 'Aleo') === $fontKey ? 'selected' : '' }}>{{ $fontLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-6 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Institution (px)</label>
                                <input type="number" step="0.5" min="14" max="60" name="certificate_institution_font_size" id="form_inst_font_size"
                                       value="{{ old('certificate_institution_font_size', $settings['institution_font_size'] ?? '31') }}"
                                       class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Cert Title (px)</label>
                                <input type="number" step="0.5" min="16" max="64" name="certificate_title_font_size" id="form_title_font_size"
                                       value="{{ old('certificate_title_font_size', $settings['title_font_size'] ?? '35') }}"
                                       class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Recipient (px)</label>
                                <input type="number" step="0.5" min="16" max="64" name="certificate_recipient_font_size" id="form_recipient_font_size"
                                       value="{{ old('certificate_recipient_font_size', $settings['recipient_font_size'] ?? '32') }}"
                                       class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Course Title (px)</label>
                                <input type="number" step="0.5" min="14" max="56" name="certificate_course_font_size" id="form_course_font_size"
                                       value="{{ old('certificate_course_font_size', $settings['course_font_size'] ?? '30') }}"
                                       class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Body Text (px)</label>
                                <input type="number" step="0.5" min="10" max="32" name="certificate_body_font_size" id="form_body_font_size"
                                       value="{{ old('certificate_body_font_size', $settings['body_font_size'] ?? '16.5') }}"
                                       class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">ID &amp; Date (px)</label>
                                <input type="number" step="0.5" min="8" max="24" name="certificate_meta_font_size" id="form_meta_font_size"
                                       value="{{ old('certificate_meta_font_size', $settings['meta_font_size'] ?? '11') }}"
                                       class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-700 mb-1">Seal Text (px)</label>
                                <input type="number" step="0.5" min="8" max="26" name="certificate_seal_font_size" id="form_seal_font_size"
                                       value="{{ old('certificate_seal_font_size', $settings['seal_font_size'] ?? '15') }}"
                                       class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2.5 bg-white">
                            </div>
                        </div>

                        <!-- Design & Color Palette Controls -->
                        <div class="pt-3 border-t border-slate-100 space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-[#063b96]">Design Colors &amp; Subtitle</span>
                                <button type="button" id="reset_design_colors_btn" class="text-[11px] font-bold text-[#063b96] hover:underline cursor-pointer">
                                    Reset Colors
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-5 gap-2.5">
                                <div class="sm:col-span-1">
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Subtitle</label>
                                    <input type="text" name="certificate_subtitle" id="form_cert_subtitle"
                                           value="{{ old('certificate_subtitle', $settings['subtitle'] ?? 'OF PARTICIPATION') }}"
                                           placeholder="OF PARTICIPATION"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Sidebar / Heading</label>
                                    <input type="color" name="certificate_sidebar_color" id="form_sidebar_color"
                                           value="{{ old('certificate_sidebar_color', $settings['sidebar_color'] ?? '#07398d') }}"
                                           class="w-full h-8 rounded-lg border border-gray-300 p-0.5 bg-white cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Border / Accent</label>
                                    <input type="color" name="certificate_accent_color" id="form_accent_color"
                                           value="{{ old('certificate_accent_color', $settings['accent_color'] ?? '#AF804F') }}"
                                           class="w-full h-8 rounded-lg border border-gray-300 p-0.5 bg-white cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Name Font Color</label>
                                    <input type="color" name="certificate_recipient_color" id="form_recipient_color"
                                           value="{{ old('certificate_recipient_color', $settings['recipient_color'] ?? '#410200') }}"
                                           class="w-full h-8 rounded-lg border border-gray-300 p-0.5 bg-white cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Course Title</label>
                                    <input type="color" name="certificate_course_color" id="form_course_color"
                                           value="{{ old('certificate_course_color', $settings['course_color'] ?? '#003878') }}"
                                           class="w-full h-8 rounded-lg border border-gray-300 p-0.5 bg-white cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Title Color</label>
                                    <input type="color" name="certificate_title_color" id="form_title_color"
                                           value="{{ old('certificate_title_color', $settings['title_color'] ?? '#1c2a4c') }}"
                                           class="w-full h-8 rounded-lg border border-gray-300 p-0.5 bg-white cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Body Text</label>
                                    <input type="color" name="certificate_body_color" id="form_body_color"
                                           value="{{ old('certificate_body_color', $settings['body_color'] ?? '#1c2a4c') }}"
                                           class="w-full h-8 rounded-lg border border-gray-300 p-0.5 bg-white cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">ID &amp; Date</label>
                                    <input type="color" name="certificate_meta_color" id="form_meta_color"
                                           value="{{ old('certificate_meta_color', $settings['meta_color'] ?? '#23bf70') }}"
                                           class="w-full h-8 rounded-lg border border-gray-300 p-0.5 bg-white cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Manual Element Layout & Position Studio -->
                        <div class="pt-3 border-t border-slate-100 space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-[#063b96]">Manual Element Layout &amp; Position (%)</span>
                                <button type="button" id="reset_layout_btn" class="text-[11px] font-bold text-[#063b96] hover:underline cursor-pointer">
                                    Reset Layout
                                </button>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Logo Top (%)</label>
                                    <input type="number" step="0.5" min="0" max="25" name="certificate_logo_top" id="form_logo_top"
                                           value="{{ old('certificate_logo_top', $settings['logo_top'] ?? '1.5') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Logo Width (%)</label>
                                    <input type="number" step="0.5" min="10" max="70" name="certificate_logo_width" id="form_logo_width"
                                           value="{{ old('certificate_logo_width', $settings['logo_width'] ?? '34.5') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Title Top (%)</label>
                                    <input type="number" step="0.2" min="5" max="40" name="certificate_title_top" id="form_title_top"
                                           value="{{ old('certificate_title_top', $settings['title_top'] ?? '18.6') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Conferment Top (%)</label>
                                    <input type="number" step="0.2" min="15" max="50" name="certificate_conferment_top" id="form_conferment_top"
                                           value="{{ old('certificate_conferment_top', $settings['conferment_top'] ?? '29.8') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Name Top (%)</label>
                                    <input type="number" step="0.2" min="20" max="60" name="certificate_recipient_top" id="form_recipient_top"
                                           value="{{ old('certificate_recipient_top', $settings['recipient_top'] ?? '34.8') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Body Top (%)</label>
                                    <input type="number" step="0.2" min="25" max="70" name="certificate_body_top" id="form_body_top"
                                           value="{{ old('certificate_body_top', $settings['body_top'] ?? '45.4') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Course Top (%)</label>
                                    <input type="number" step="0.2" min="35" max="80" name="certificate_course_top" id="form_course_top"
                                           value="{{ old('certificate_course_top', $settings['course_top'] ?? '54.0') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">ID/Date Top (%)</label>
                                    <input type="number" step="0.2" min="45" max="85" name="certificate_meta_top" id="form_meta_top"
                                           value="{{ old('certificate_meta_top', $settings['meta_top'] ?? '67.2') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">ID/Date Left (%)</label>
                                    <input type="number" step="0.2" min="0" max="45" name="certificate_meta_left" id="form_meta_left"
                                           value="{{ old('certificate_meta_left', $settings['meta_left'] ?? '5.2') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">QR Top (%)</label>
                                    <input type="number" step="0.5" min="40" max="85" name="certificate_qr_top" id="form_qr_top"
                                           value="{{ old('certificate_qr_top', $settings['qr_top'] ?? '62.0') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">QR Size (%)</label>
                                    <input type="number" step="0.5" min="8" max="32" name="certificate_qr_width" id="form_qr_width"
                                           value="{{ old('certificate_qr_width', $settings['qr_width'] ?? '19.8') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Footer Bottom (%)</label>
                                    <input type="number" step="0.2" min="1" max="20" name="certificate_footer_bottom" id="form_footer_bottom"
                                           value="{{ old('certificate_footer_bottom', $settings['footer_bottom'] ?? '6.2') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Seal Size (cqw)</label>
                                    <input type="number" step="0.5" min="6" max="24" name="certificate_seal_size" id="form_seal_size"
                                           value="{{ old('certificate_seal_size', $settings['seal_size'] ?? '14.8') }}"
                                           class="w-full text-xs rounded-lg border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-1.5 px-2 bg-white">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full py-3 bg-[#063b96] hover:bg-[#082860] text-white rounded-xl font-black text-xs sm:text-sm tracking-wide uppercase shadow-md hover:shadow-xl transition-all cursor-pointer">
                    💾 Save Certificate Customizations & Template
                </button>
            </div>
        </form>
    </div>

    <!-- Right Column: Interactive Live Preview Frame (5 Cols) -->
    <div class="xl:col-span-5 space-y-4 sticky top-6">
        <div class="flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-black text-gray-900 flex items-center gap-2">
                <span>Real-Time Certificate Preview</span>
            </h2>
            <span class="text-xs text-slate-500 font-semibold">Active: {{ ucfirst(str_replace('_', ' ', $settings['template'])) }}</span>
        </div>

        <div class="bg-slate-900 rounded-3xl p-3 sm:p-5 shadow-2xl border border-slate-800">
            <!-- Simulated Certificate Card -->
            <div id="previewCard" class="w-full bg-[#fcf8f6] rounded-2xl p-4 sm:p-6 border-4 shadow-inner relative overflow-hidden transition-all duration-300
                {{ $settings['template'] === 'acs_signature' ? 'border-[#b91c1c]' : '' }}
                {{ $settings['template'] === 'classic_navy' ? 'border-[#063b96]' : '' }}
                {{ $settings['template'] === 'modern_emerald' ? 'border-[#064e3b]' : '' }}
                {{ $settings['template'] === 'crimson_prestige' ? 'border-[#880102]' : '' }}
                {{ $settings['template'] === 'minimalist_corporate' ? 'border-slate-800' : '' }}
            ">
                <!-- Inner Border -->
                <div class="border p-3 sm:p-4 rounded-lg relative text-center space-y-2
                    {{ $settings['template'] === 'acs_signature' ? 'border-transparent bg-[#fcf8f6]' : '' }}
                    {{ $settings['template'] === 'classic_navy' ? 'border-[#b89758] bg-gradient-to-b from-[#fffefc] to-[#faf6ef]' : '' }}
                    {{ $settings['template'] === 'modern_emerald' ? 'border-[#10b981] bg-gradient-to-b from-white to-[#f0fdf4]' : '' }}
                    {{ $settings['template'] === 'crimson_prestige' ? 'border-[#063b96] bg-gradient-to-b from-white to-[#fff1f2]' : '' }}
                    {{ $settings['template'] === 'minimalist_corporate' ? 'border-slate-300 bg-white' : '' }}
                ">
                    @php
                        $prevHeaderLogo = !empty($settings['logo_url'])
                            ? (str_starts_with($settings['logo_url'], 'http') ? $settings['logo_url'] : asset(ltrim($settings['logo_url'], '/')))
                            : (file_exists(public_path('images/certificate-logo-gold.png')) ? asset('images/certificate-logo-gold.png') : (\App\Models\Setting::getVal('site_logo') ?: asset('images/logo.png')));
                        $prevInnerSealLogo = !empty($settings['seal_logo_url'])
                            ? (str_starts_with($settings['seal_logo_url'], 'http') ? $settings['seal_logo_url'] : asset(ltrim($settings['seal_logo_url'], '/')))
                            : $prevHeaderLogo;
                        $prevSealParts = explode('•', $settings['seal_text'] ?? 'OFFICIAL SEAL • VERIFIED');
                        $prevSealLine1 = trim($prevSealParts[0] ?? 'OFFICIAL SEAL');
                        $prevSealLine2 = trim($prevSealParts[1] ?? 'VERIFIED');
                    @endphp

                    <!-- Center Watermark Preview -->
                    <div id="prev_watermark_wrap" class="pointer-events-none absolute inset-0 flex items-center justify-center z-0 {{ ($settings['show_watermark'] ?? '1') == '1' ? '' : 'hidden' }}">
                        <img id="prev_watermark_img" src="{{ $prevHeaderLogo }}" class="w-40 h-40 object-contain opacity-[0.15] translate-y-2" alt="">
                    </div>

                    <!-- Crest / Logo -->
                    <div class="relative z-10 flex justify-center">
                        <img id="prev_header_logo" src="{{ $prevHeaderLogo }}" class="w-16 h-16 object-contain drop-shadow-xs" alt="ACS Crest">
                    </div>

                    <!-- Institution Name (Live Updated) -->
                    <h3 id="prev_inst_name" class="relative z-10 font-serif text-sm sm:text-base font-black uppercase tracking-wider text-[#5b1415]">
                        {{ $settings['institution_name'] }}
                    </h3>
                    <p id="prev_tagline" class="relative z-10 text-[8px] font-bold text-[#991b1b] uppercase tracking-widest">
                        {{ $settings['tagline'] }}
                    </p>

                    <!-- Honor Title -->
                    <div class="relative z-10 pt-1.5">
                        <span id="prev_cert_title" class="text-[11px] font-serif uppercase tracking-wider font-bold text-[#641617] block">
                            {{ $settings['title'] }}
                        </span>
                        <p id="prev_confer_text" class="text-[8px] text-gray-700 font-semibold mt-0.5">
                            {{ $settings['conferment_text'] }}
                        </p>
                        
                        <!-- Student Name -->
                        <div class="my-1 py-1 border-b border-dotted border-gray-400 inline-block min-w-[180px]">
                            <h4 id="prev_recipient_name" class="font-sans text-xs sm:text-sm font-black uppercase" style="color: {{ $settings['recipient_color'] ?? '#410200' }};">
                                {{ $sampleUser->name }}
                            </h4>
                        </div>

                        <!-- Course Title -->
                        <h5 id="prev_course_title" class="text-[10px] sm:text-xs font-black text-[#4f4e8c] uppercase tracking-wide mt-1">
                            {{ $sampleCourse->title }}
                        </h5>
                    </div>

                    <!-- Signatures, Verification QR & Scalloped Gold Seal Preview -->
                    <div class="relative z-10 pt-3 border-t border-gray-200/70 flex items-end justify-between text-[8px] text-gray-600 gap-2">
                        <!-- Left: QR Code + Serial & Date -->
                        <div class="text-left space-y-0.5">
                            <div id="prev_qr_wrap" class="inline-block bg-white p-1 rounded-xs shadow-xs border border-slate-200 mb-1 {{ ($settings['show_qr_code'] ?? '1') == '1' ? '' : 'hidden' }}">
                                <svg viewBox="0 0 21 21" class="w-9 h-9 block" shape-rendering="crispEdges">
                                    <rect width="21" height="21" fill="#ffffff"/>
                                    <path d="M0,0 h7 v7 h-7 z M1,1 v5 h5 v-5 z M2,2 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                    <path d="M14,0 h7 v7 h-7 z M15,1 v5 h5 v-5 z M16,2 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                    <path d="M0,14 h7 v7 h-7 z M1,15 v5 h5 v-5 z M2,16 h3 v3 h-3 z" fill="#111827" fill-rule="evenodd"/>
                                    <rect x="8" y="6" width="1" height="1" fill="#111827"/><rect x="10" y="6" width="1" height="1" fill="#111827"/><rect x="12" y="6" width="1" height="1" fill="#111827"/>
                                    <rect x="6" y="8" width="1" height="1" fill="#111827"/><rect x="6" y="10" width="1" height="1" fill="#111827"/><rect x="6" y="12" width="1" height="1" fill="#111827"/>
                                    <rect x="9" y="9" width="2" height="2" fill="#111827"/><rect x="12" y="11" width="2" height="1" fill="#111827"/><rect x="8" y="14" width="3" height="2" fill="#111827"/>
                                    <rect x="14" y="9" width="2" height="2" fill="#111827"/><rect x="16" y="13" width="3" height="2" fill="#111827"/><rect x="13" y="16" width="2" height="3" fill="#111827"/>
                                </svg>
                            </div>
                            <span class="font-sans font-extrabold block text-[#2e6f23] text-[7px]"><span id="prev_id_label_text">{{ $settings['id_label'] ?? 'CERTIFICATE ID:' }}</span> ACS-2026-DEMO</span>
                            <span class="font-sans font-extrabold block text-[#2e6f23] text-[7px]"><span id="prev_date_label_text">{{ $settings['date_label'] ?? 'DATE:' }}</span> {{ strtoupper(now()->format('d F Y')) }}</span>
                        </div>

                        <!-- Center: Realistic Scalloped Gold Seal with Curved Text & Inner Logo -->
                        <div id="prev_seal_wrap" class="relative w-16 h-16 flex items-center justify-center shrink-0 {{ ($settings['show_seal'] ?? '1') == '1' ? '' : 'hidden' }}">
                            <img id="prev_seal_bg_img" src="{{ asset(ltrim($settings['seal_bg_url'] ?? '/images/certificate-gold-seal.png', '/')) }}" class="w-full h-full object-contain drop-shadow-xs" alt="Gold Seal">
                            <svg id="prev_seal_text_svg" viewBox="0 0 240 240" class="w-full h-full absolute inset-0 pointer-events-none {{ ($settings['show_seal_text'] ?? '1') == '1' ? '' : 'hidden' }}">
                                <defs>
                                    <path id="adminPrevTopArc" d="M 63,120 A 57,57 0 1,1 177,120" />
                                    <path id="adminPrevBottomArc" d="M 53,120 A 67,67 0 0,0 187,120" />
                                </defs>
                                <circle cx="120" cy="120" r="71" fill="none" stroke="#6b4308" stroke-width="1.3" stroke-dasharray="3 2.5" opacity="0.55" />
                                <circle cx="120" cy="120" r="49" fill="none" stroke="#6b4308" stroke-width="1.1" opacity="0.45" />
                                <text fill="#3d2200" font-size="13.5" font-weight="900" font-family="Montserrat, Cinzel, sans-serif" letter-spacing="2.2">
                                    <textPath id="prev_seal_top_text" href="#adminPrevTopArc" startOffset="50%" text-anchor="middle">{{ strtoupper($prevSealLine1) }}</textPath>
                                </text>
                                <text fill="#3d2200" font-size="13" font-weight="900" font-family="Montserrat, Cinzel, sans-serif" letter-spacing="2.2">
                                    <textPath id="prev_seal_bottom_text" href="#adminPrevBottomArc" startOffset="50%" text-anchor="middle">{{ !empty($prevSealLine2) ? '★ ' . strtoupper($prevSealLine2) . ' ★' : '' }}</textPath>
                                </text>
                            </svg>
                            <div id="prev_seal_logo_wrap" class="absolute inset-0 flex items-center justify-center pointer-events-none z-10 {{ ($settings['show_seal_logo'] ?? '1') == '1' ? '' : 'hidden' }}">
                                <img id="prev_seal_logo_img" src="{{ $prevInnerSealLogo }}" class="w-[38%] h-[38%] object-contain drop-shadow-xs" alt="Seal Logo">
                            </div>
                        </div>

                        <!-- Right: Signatory -->
                        <div class="text-right">
                            <span id="prev_sig_name" class="font-serif italic font-bold block text-gray-900 text-[10px] border-b border-dotted border-gray-800 pb-0.5">
                                {{ $settings['primary_signatory_name'] }}
                            </span>
                            <span id="prev_sig_title" class="block text-gray-800 font-bold text-[7px] uppercase pt-0.5">
                                {{ $settings['primary_signatory_title'] }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="pt-3 text-center">
                <a href="{{ route('admin.certificates.preview') }}" target="_blank" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 hover:underline inline-flex items-center gap-1.5">
                    <span>Open Interactive Full-Screen A4 Print Preview</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
        </div>
    </div>

</div>

<!-- Issued Certificates Registry Table -->
<div class="bg-white rounded-2xl sm:rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-gray-100 overflow-hidden">
    <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base sm:text-lg font-black text-gray-900">Issued Certificates Registry</h2>
            <p class="text-gray-500 text-xs sm:text-sm">Publicly verifiable credentials issued across all ACS Academy cohorts.</p>
        </div>

        <form method="GET" action="{{ route('admin.certificates.index') }}" class="flex items-center gap-2">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Search serial, student, course..." 
                class="text-xs sm:text-sm rounded-xl border-gray-300 focus:border-[#063b96] focus:ring-[#063b96] py-2 px-3 w-64"
            >
            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.certificates.index') }}" class="p-2 text-xs text-gray-500 hover:text-gray-800">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[720px]">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-700">
                    <th class="p-3.5 sm:p-4 font-bold">Certificate Serial</th>
                    <th class="p-3.5 sm:p-4 font-bold">Student Name</th>
                    <th class="p-3.5 sm:p-4 font-bold">Course Title</th>
                    <th class="p-3.5 sm:p-4 font-bold">Issue Date</th>
                    <th class="p-3.5 sm:p-4 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs sm:text-sm divide-y divide-gray-50">
                @forelse($certificates as $cert)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-3.5 sm:p-4">
                            <span class="font-mono font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded text-xs">
                                {{ $cert->certificate_number }}
                            </span>
                        </td>
                        <td class="p-3.5 sm:p-4">
                            <div class="font-bold text-gray-900">{{ $cert->user?->name ?? 'Candidate' }}</div>
                            <div class="text-gray-500 text-xs">{{ $cert->user?->email ?? 'N/A' }}</div>
                        </td>
                        <td class="p-3.5 sm:p-4">
                            <div class="font-medium text-gray-800">{{ $cert->course?->title ?? 'Course Archived' }}</div>
                            @if(!empty($cert->course?->batch) && $cert->course?->show_batch)
                                <span class="text-[10px] text-emerald-700 font-bold font-mono">{{ $cert->course->batch }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 sm:p-4 text-gray-600 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($cert->issue_date)->format('M d, Y') }}
                        </td>
                        <td class="p-3.5 sm:p-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a 
                                    href="{{ route('courses.certificate', ['course' => $cert->course_id, 'as_user' => $cert->user_id]) }}" 
                                    target="_blank" 
                                    class="text-primary hover:text-green-700 font-bold text-xs bg-primary/10 hover:bg-primary/20 px-2.5 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Print / View</span>
                                </a>

                                <a 
                                    href="{{ route('certificate.verify', $cert->certificate_number) }}" 
                                    target="_blank" 
                                    class="text-slate-600 hover:text-slate-900 font-medium text-xs bg-gray-100 hover:bg-gray-200 px-2.5 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1"
                                    title="Verify Public Registry"
                                >
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Verify</span>
                                </a>

                                <form method="POST" action="{{ route('admin.certificates.notify', $cert) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sky-700 hover:text-sky-900 font-bold text-xs bg-sky-50 hover:bg-sky-100 px-2.5 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1 cursor-pointer" title="Send In-App Notification & Email">
                                        <i class="fas fa-paper-plane text-[10px]"></i>
                                        <span>Notify &amp; Email</span>
                                    </button>
                                </form>

                                @php
                                    $certWaPhone = preg_replace('/[^0-9]/', '', (string) ($cert->user?->effective_phone ?? $cert->user?->phone ?? ''));
                                    if (strlen($certWaPhone) === 11 && str_starts_with($certWaPhone, '01')) {
                                        $certWaPhone = '88' . $certWaPhone;
                                    }
                                    $certWaText = rawurlencode("Congratulations {$cert->user?->name}! Your official certificate ({$cert->certificate_number}) for {$cert->course?->title} is ready: " . route('certificate.verify', $cert->certificate_number));
                                @endphp
                                @if($certWaPhone !== '')
                                    <a href="https://wa.me/{{ $certWaPhone }}?text={{ $certWaText }}" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:text-emerald-900 font-bold text-xs bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1.5 rounded-lg transition-colors inline-flex items-center gap-1" title="Send Certificate via WhatsApp">
                                        <i class="fab fa-whatsapp text-xs"></i>
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('admin.certificates.destroy', $cert) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Revoke this digital certificate? The student will no longer have access to it until re-issued.')" class="text-red-600 hover:text-red-800 font-medium text-xs hover:bg-red-50 px-2 py-1.5 rounded-lg transition-colors cursor-pointer">
                                        Revoke
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-gray-500">
                            <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                            </div>
                            <div class="font-bold text-gray-700">No certificates generated yet</div>
                            <div class="text-xs text-gray-400 mt-1">When students complete courses with enabled certificates, they will appear here.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($certificates->hasPages())
        <div class="p-4 border-t border-gray-100 flex justify-center">
            {{ $certificates->links() }}
        </div>
    @endif
</div>

<!-- Live Typing Preview Script -->
<script>
    const nameInput = document.getElementById('form_inst_name');
    const prevName = document.getElementById('prev_inst_name');
    if (nameInput && prevName) {
        nameInput.addEventListener('input', function() {
            prevName.innerText = this.value || 'ACS Academy';
        });
    }

    const tagInput = document.getElementById('form_tagline');
    const prevTag = document.getElementById('prev_tagline');
    if (tagInput && prevTag) {
        tagInput.addEventListener('input', function() {
            prevTag.innerText = this.value || 'ACS Academy • Learn, Grow, Lead.';
        });
    }

    const certTitleInput = document.getElementById('form_cert_title');
    const prevCertTitle = document.getElementById('prev_cert_title');
    if (certTitleInput && prevCertTitle) {
        certTitleInput.addEventListener('input', function() {
            prevCertTitle.innerText = this.value || 'Executive Certification of Mastery';
        });
    }

    const conferInput = document.getElementById('form_confer_text');
    const prevConfer = document.getElementById('prev_confer_text');
    if (conferInput && prevConfer) {
        conferInput.addEventListener('input', function() {
            prevConfer.innerText = this.value || 'This is proudly presented and conferred upon';
        });
    }

    const sigNameInput = document.getElementById('form_sig1_name');
    const prevSigName = document.getElementById('prev_sig_name');
    if (sigNameInput && prevSigName) {
        sigNameInput.addEventListener('input', function() {
            prevSigName.innerText = this.value || 'Academic Director';
        });
    }

    const sigTitleInput = document.getElementById('form_sig1_title');
    const prevSigTitle = document.getElementById('prev_sig_title');
    if (sigTitleInput && prevSigTitle) {
        sigTitleInput.addEventListener('input', function() {
            prevSigTitle.innerText = this.value || 'Academic Director';
        });
    }

    const sealTextInput = document.getElementById('form_seal_text');
    const prevSealTopText = document.getElementById('prev_seal_top_text');
    const prevSealBottomText = document.getElementById('prev_seal_bottom_text');
    if (sealTextInput) {
        sealTextInput.addEventListener('input', function() {
            const parts = (this.value || 'OFFICIAL SEAL • VERIFIED').split('•');
            const topStr = (parts[0] || '').trim().toUpperCase();
            const botStr = (parts[1] || '').trim().toUpperCase();
            if (prevSealTopText) prevSealTopText.textContent = topStr;
            if (prevSealBottomText) prevSealBottomText.textContent = botStr ? `★ ${botStr} ★` : '';
        });
    }

    // Live Toggle Checkboxes (Seal, Seal Logo, Seal Text, QR Code, Watermark)
    const bindToggle = (checkboxId, targetId) => {
        const cb = document.getElementById(checkboxId);
        const target = document.getElementById(targetId);
        if (cb && target) {
            cb.addEventListener('change', function() {
                target.classList.toggle('hidden', !this.checked);
            });
        }
    };
    bindToggle('form_show_seal', 'prev_seal_wrap');
    bindToggle('form_show_seal_logo', 'prev_seal_logo_wrap');
    bindToggle('form_show_seal_text', 'prev_seal_text_svg');
    bindToggle('form_show_qr_code', 'prev_qr_wrap');
    bindToggle('form_show_watermark', 'prev_watermark_wrap');

    // Live Custom Logo URL & File Inputs
    const defaultLogoSrc = "{{ file_exists(public_path('images/certificate-logo-gold.png')) ? asset('images/certificate-logo-gold.png') : asset('images/logo.png') }}";
    const defaultSealBgSrc = "{{ asset('images/certificate-gold-seal.png') }}";
    const logoUrlInput = document.getElementById('form_logo_url');
    const sealLogoUrlInput = document.getElementById('form_seal_logo_url');
    const sealBgUrlInput = document.getElementById('form_seal_bg_url');
    const logoFileInput = document.getElementById('form_logo_file');
    const sealLogoFileInput = document.getElementById('form_seal_logo_file');
    const sealBgFileInput = document.getElementById('form_seal_bg_file');
    const prevHeaderLogo = document.getElementById('prev_header_logo');
    const prevWatermarkImg = document.getElementById('prev_watermark_img');
    const prevSealLogoImg = document.getElementById('prev_seal_logo_img');
    const prevSealBgImg = document.getElementById('prev_seal_bg_img');

    const idLabelInput = document.getElementById('form_id_label');
    const prevIdLabelText = document.getElementById('prev_id_label_text');
    if (idLabelInput && prevIdLabelText) {
        idLabelInput.addEventListener('input', function() {
            prevIdLabelText.innerText = this.value || 'CERTIFICATE ID:';
        });
    }
    const dateLabelInput = document.getElementById('form_date_label');
    const prevDateLabelText = document.getElementById('prev_date_label_text');
    if (dateLabelInput && prevDateLabelText) {
        dateLabelInput.addEventListener('input', function() {
            prevDateLabelText.innerText = this.value || 'DATE:';
        });
    }

    if (logoUrlInput) {
        logoUrlInput.addEventListener('input', function() {
            const url = this.value.trim() || defaultLogoSrc;
            if (prevHeaderLogo) prevHeaderLogo.src = url;
            if (prevWatermarkImg) prevWatermarkImg.src = url;
        });
    }
    if (sealLogoUrlInput) {
        sealLogoUrlInput.addEventListener('input', function() {
            const url = this.value.trim() || defaultLogoSrc;
            if (prevSealLogoImg) prevSealLogoImg.src = url;
        });
    }
    if (sealBgUrlInput) {
        sealBgUrlInput.addEventListener('input', function() {
            const url = this.value.trim() || defaultSealBgSrc;
            if (prevSealBgImg) prevSealBgImg.src = url;
        });
    }

    // Instant FileReader preview when Admin selects an image file
    if (logoFileInput) {
        logoFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (prevHeaderLogo) prevHeaderLogo.src = e.target.result;
                    if (prevWatermarkImg) prevWatermarkImg.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    if (sealLogoFileInput) {
        sealLogoFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (prevSealLogoImg) prevSealLogoImg.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    if (sealBgFileInput) {
        sealBgFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (prevSealBgImg) prevSealBgImg.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Dynamic Template Preview Switcher
    const templateRadios = document.querySelectorAll('input[name="certificate_template"]');
    const previewCard = document.getElementById('previewCard');
    const previewInner = previewCard ? previewCard.querySelector('.border') : null;
    const printLinks = document.querySelectorAll('a[href*="admin/certificates/preview"]');
    const activeTemplateBadge = document.querySelector('.sticky span.text-slate-500');

    const templateStyles = {
        'acs_signature': {
            name: 'ACS Official Signature',
            cardBorder: 'border-[#b91c1c]',
            innerBorder: 'border-transparent bg-[#fcf8f6]',
            instColor: 'text-[#5b1415]',
            tagColor: 'text-[#991b1b]',
            titleColor: 'text-[#641617]',
            courseColor: 'text-[#cc292b]'
        },
        'classic_navy': {
            name: 'Classic Navy & Gold',
            cardBorder: 'border-[#063b96]',
            innerBorder: 'border-[#b89758] bg-gradient-to-b from-[#fffefc] to-[#faf6ef]',
            instColor: 'text-[#063b96]',
            tagColor: 'text-[#c4040a]',
            titleColor: 'text-[#b89758]',
            courseColor: 'text-[#c4040a]'
        },
        'modern_emerald': {
            name: 'Modern Luxury Emerald',
            cardBorder: 'border-[#064e3b]',
            innerBorder: 'border-[#10b981] bg-gradient-to-b from-white to-[#f0fdf4]',
            instColor: 'text-[#064e3b]',
            tagColor: 'text-[#059669]',
            titleColor: 'text-[#047857]',
            courseColor: 'text-[#059669]'
        },
        'crimson_prestige': {
            name: 'Crimson Prestige',
            cardBorder: 'border-[#880102]',
            innerBorder: 'border-[#063b96] bg-gradient-to-b from-white to-[#fff1f2]',
            instColor: 'text-[#880102]',
            tagColor: 'text-[#063b96]',
            titleColor: 'text-[#880102]',
            courseColor: 'text-[#880102]'
        },
        'minimalist_corporate': {
            name: 'Minimalist Corporate',
            cardBorder: 'border-slate-800',
            innerBorder: 'border-slate-300 bg-white',
            instColor: 'text-slate-900',
            tagColor: 'text-slate-600',
            titleColor: 'text-slate-700',
            courseColor: 'text-slate-800'
        }
    };

    function applyTemplate(tmpl) {
        if (!previewCard || !templateStyles[tmpl]) return;
        const s = templateStyles[tmpl];

        // Update card borders
        previewCard.className = `w-full bg-[#fffdf9] rounded-2xl p-4 sm:p-6 border-4 shadow-inner relative overflow-hidden transition-all duration-300 ${s.cardBorder}`;
        if (previewInner) {
            previewInner.className = `border p-3 sm:p-4 rounded-lg relative text-center space-y-2 ${s.innerBorder}`;
        }
        if (prevName) prevName.className = `font-serif text-sm sm:text-base font-black uppercase tracking-wider ${s.instColor}`;
        if (prevTag) prevTag.className = `text-[8px] font-bold uppercase tracking-widest ${s.tagColor}`;
        if (prevCertTitle) prevCertTitle.className = `text-[9px] font-serif uppercase tracking-widest font-bold block ${s.titleColor}`;
        if (activeTemplateBadge) activeTemplateBadge.innerText = `Active: ${s.name}`;

        // Update preview links with active template + selected fonts
        updatePreviewLinks(tmpl);
    }

    function updatePreviewLinks(tmplOverride) {
        const checkedTmpl = document.querySelector('input[name="certificate_template"]:checked');
        const tmpl = tmplOverride || (checkedTmpl ? checkedTmpl.value : '{{ $settings['template'] ?? 'acs_signature' }}');
        const tf = document.getElementById('form_title_font')?.value || 'Cinzel';
        const rf = document.getElementById('form_recipient_font')?.value || 'Alex Brush';
        const cf = document.getElementById('form_course_font')?.value || 'Anton';
        const bf = document.getElementById('form_body_font')?.value || 'Aleo';
        const tfs = document.getElementById('form_title_font_size')?.value || '35';
        const rfs = document.getElementById('form_recipient_font_size')?.value || '32';
        const cfs = document.getElementById('form_course_font_size')?.value || '30';
        const bfs = document.getElementById('form_body_font_size')?.value || '16.5';
        const mfs = document.getElementById('form_meta_font_size')?.value || '11';
        const sf = document.getElementById('form_seal_font_size')?.value || '15';
        const sub = document.getElementById('form_cert_subtitle')?.value || 'OF PARTICIPATION';
        const sc = document.getElementById('form_sidebar_color')?.value || '#07398d';
        const ac = document.getElementById('form_accent_color')?.value || '#AF804F';
        const rc = document.getElementById('form_recipient_color')?.value || '#410200';
        const tc = document.getElementById('form_title_color')?.value || '#1c2a4c';
        const bc = document.getElementById('form_body_color')?.value || '#1c2a4c';
        const cc = document.getElementById('form_course_color')?.value || '#003878';
        const mc = document.getElementById('form_meta_color')?.value || '#23bf70';
        const layoutParams = [
            ['logo_top', 'form_logo_top', '1.5'],
            ['logo_width', 'form_logo_width', '34.5'],
            ['title_top', 'form_title_top', '18.6'],
            ['conferment_top', 'form_conferment_top', '29.8'],
            ['recipient_top', 'form_recipient_top', '34.8'],
            ['body_top', 'form_body_top', '45.4'],
            ['course_top', 'form_course_top', '54.0'],
            ['meta_top', 'form_meta_top', '67.2'],
            ['meta_left', 'form_meta_left', '5.2'],
            ['qr_top', 'form_qr_top', '62.0'],
            ['qr_width', 'form_qr_width', '19.8'],
            ['footer_bottom', 'form_footer_bottom', '6.2'],
            ['seal_size', 'form_seal_size', '14.8'],
        ].map(([param, id, def]) => `${param}=${encodeURIComponent(document.getElementById(id)?.value ?? def)}`).join('&');
        const baseUrl = "{{ route('admin.certificates.preview') }}";
        const qs = `template=${encodeURIComponent(tmpl)}&subtitle=${encodeURIComponent(sub)}&title_font=${encodeURIComponent(tf)}&recipient_font=${encodeURIComponent(rf)}&course_font=${encodeURIComponent(cf)}&body_font=${encodeURIComponent(bf)}&title_font_size=${encodeURIComponent(tfs)}&recipient_font_size=${encodeURIComponent(rfs)}&course_font_size=${encodeURIComponent(cfs)}&body_font_size=${encodeURIComponent(bfs)}&meta_font_size=${encodeURIComponent(mfs)}&seal_font_size=${encodeURIComponent(sf)}&sidebar_color=${encodeURIComponent(sc)}&accent_color=${encodeURIComponent(ac)}&recipient_color=${encodeURIComponent(rc)}&title_color=${encodeURIComponent(tc)}&body_color=${encodeURIComponent(bc)}&course_color=${encodeURIComponent(cc)}&meta_color=${encodeURIComponent(mc)}&${layoutParams}`;
        printLinks.forEach(link => {
            link.href = `${baseUrl}?${qs}`;
        });
    }

    templateRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                applyTemplate(this.value);
            }
        });
    });

    // Live Typography / Font Size Preview Scaling
    const prevRecipientName = document.getElementById('prev_recipient_name');
    const prevCourseTitle = document.getElementById('prev_course_title');
    const fontBindings = [
        { inputId: 'form_inst_font_size', targetEl: prevName, defaultVal: 31 },
        { inputId: 'form_title_font_size', targetEl: prevCertTitle, defaultVal: 35 },
        { inputId: 'form_recipient_font_size', targetEl: prevRecipientName, defaultVal: 32 },
        { inputId: 'form_course_font_size', targetEl: prevCourseTitle, defaultVal: 30 },
        { inputId: 'form_body_font_size', targetEl: prevConferText, defaultVal: 16.5 },
    ];

    function syncPreviewFontSizes() {
        const previewScale = 0.45;
        fontBindings.forEach(({ inputId, targetEl, defaultVal }) => {
            const input = document.getElementById(inputId);
            if (!input || !targetEl) return;
            const px = parseFloat(input.value) || defaultVal;
            targetEl.style.fontSize = Math.max(7, (px * previewScale)).toFixed(1) + 'px';
        });
    }

    fontBindings.forEach(({ inputId }) => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', syncPreviewFontSizes);
        }
    });

    const fontFamilyBindings = [
        { selectId: 'form_title_font', targetEl: prevCertTitle, defaultVal: 'Cinzel', fallback: 'serif' },
        { selectId: 'form_recipient_font', targetEl: prevRecipientName, defaultVal: 'Alex Brush', fallback: 'cursive' },
        { selectId: 'form_course_font', targetEl: prevCourseTitle, defaultVal: 'Anton', fallback: 'sans-serif' },
        { selectId: 'form_body_font', targetEl: prevConferText, defaultVal: 'Aleo', fallback: 'serif' },
    ];

    function syncPreviewFontFamilies() {
        fontFamilyBindings.forEach(({ selectId, targetEl, defaultVal, fallback }) => {
            const sel = document.getElementById(selectId);
            if (!sel || !targetEl) return;
            const chosen = sel.value || defaultVal;
            targetEl.style.fontFamily = `'${chosen}', ${fallback}`;
        });
        updatePreviewLinks();
    }

    fontFamilyBindings.forEach(({ selectId }) => {
        const sel = document.getElementById(selectId);
        if (sel) {
            sel.addEventListener('change', syncPreviewFontFamilies);
        }
    });

    const resetFontBtn = document.getElementById('reset_font_sizes_btn');
    ['form_seal_font_size', 'form_meta_font_size', 'form_title_font_size', 'form_recipient_font_size', 'form_course_font_size', 'form_body_font_size', 'form_cert_subtitle', 'form_logo_top', 'form_logo_width', 'form_title_top', 'form_conferment_top', 'form_recipient_top', 'form_body_top', 'form_course_top', 'form_meta_top', 'form_meta_left', 'form_qr_top', 'form_qr_width', 'form_footer_bottom', 'form_seal_size'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', updatePreviewLinks);
    });
    const resetLayoutBtn = document.getElementById('reset_layout_btn');
    if (resetLayoutBtn) {
        resetLayoutBtn.addEventListener('click', function () {
            const layoutDefaults = {
                form_logo_top: '1.5',
                form_logo_width: '34.5',
                form_title_top: '18.6',
                form_conferment_top: '29.8',
                form_recipient_top: '34.8',
                form_body_top: '45.4',
                form_course_top: '54.0',
                form_meta_top: '67.2',
                form_meta_left: '5.2',
                form_qr_top: '62.0',
                form_qr_width: '19.8',
                form_footer_bottom: '6.2',
                form_seal_size: '14.8',
            };
            Object.entries(layoutDefaults).forEach(([k, v]) => {
                const el = document.getElementById(k);
                if (el) el.value = v;
            });
            updatePreviewLinks();
        });
    }
    ['form_sidebar_color', 'form_accent_color', 'form_recipient_color', 'form_title_color', 'form_body_color', 'form_course_color', 'form_meta_color'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function () {
                if (id === 'form_recipient_color' && prevRecipientName) prevRecipientName.style.color = el.value;
                if (id === 'form_course_color' && prevCourseTitle) prevCourseTitle.style.color = el.value;
                updatePreviewLinks();
            });
        }
    });
    const resetColorsBtn = document.getElementById('reset_design_colors_btn');
    if (resetColorsBtn) {
        resetColorsBtn.addEventListener('click', function () {
            const defaults = {
                form_cert_subtitle: 'OF PARTICIPATION',
                form_sidebar_color: '#07398d',
                form_accent_color: '#AF804F',
                form_recipient_color: '#410200',
                form_title_color: '#1c2a4c',
                form_body_color: '#1c2a4c',
                form_course_color: '#003878',
                form_meta_color: '#23bf70',
            };
            Object.entries(defaults).forEach(([k, v]) => {
                const el = document.getElementById(k);
                if (el) el.value = v;
            });
            if (prevRecipientName) prevRecipientName.style.color = '#410200';
            if (prevCourseTitle) prevCourseTitle.style.color = '#003878';
            updatePreviewLinks();
        });
    }
    if (resetFontBtn) {
        resetFontBtn.addEventListener('click', function() {
            fontBindings.forEach(({ inputId, defaultVal }) => {
                const input = document.getElementById(inputId);
                if (input) input.value = defaultVal;
            });
            if (sealFontSizeInput) sealFontSizeInput.value = 15;
            fontFamilyBindings.forEach(({ selectId, defaultVal }) => {
                const sel = document.getElementById(selectId);
                if (sel) sel.value = defaultVal;
            });
            syncPreviewFontSizes();
            syncPreviewFontFamilies();
        });
    }
    syncPreviewFontSizes();
    syncPreviewFontFamilies();
</script>
<link href="https://fonts.googleapis.com/css2?family=Aleo:wght@400;700&family=Alex+Brush&family=Allura&family=Anton&family=Bebas+Neue&family=Cinzel:wght@600;700;800&family=Cinzel+Decorative:wght@700;900&family=Cormorant+Garamond:wght@600;700&family=Dancing+Script:wght@600;700&family=EB+Garamond:wght@500;700&family=Great+Vibes&family=Libre+Baskerville:wght@700&family=Lora:wght@400;700&family=Merriweather:wght@400;700&family=Montserrat:wght@500;700;800&family=Open+Sans:wght@400;600;700&family=Oswald:wght@600;700&family=Parisienne&family=Pinyon+Script&family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=Poppins:wght@500;600;700;800&family=Roboto:wght@500;700&family=Sacramento&family=Tangerine:wght@700&display=swap" rel="stylesheet">
@endsection
