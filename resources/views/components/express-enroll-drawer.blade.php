<!-- Express Course Enrollment Drawer (30-Second Rapid Conversion Engine - Liquid Glass Edition) -->
@php
    $drawerMerchantAccount = '01917-054664';
    try {
        $activeMethodAccount = \App\Models\PaymentMethod::where('is_active', true)->orderBy('id')->value('account_number');
        $drawerMerchantAccount = $activeMethodAccount ?: \App\Models\Setting::getVal('contact_hotline', '01917-054664');
    } catch (\Throwable $e) {
        $drawerMerchantAccount = \App\Models\Setting::getVal('contact_hotline', '01917-054664');
    }
    $drawerMerchantDigits = preg_replace('/[^0-9]/', '', (string) $drawerMerchantAccount) ?: '01917054664';
    $rawDrawerWa = \App\Models\Setting::getVal('contact_whatsapp', \App\Models\Setting::getVal('contact_hotline', '8801917054664'));
    $drawerWaDigits = preg_replace('/[^0-9]/', '', (string) $rawDrawerWa) ?: '8801917054664';
    if (strlen($drawerWaDigits) === 11 && str_starts_with($drawerWaDigits, '01')) {
        $drawerWaDigits = '88' . $drawerWaDigits;
    }
@endphp
<script>
(function() {
    function initDrawer() {
        if (!window.Alpine) return;
        window.Alpine.data('expressEnrollDrawer', () => ({
            isOpen: false,
            step: 1,
            isSubmitting: false,
            isSuccess: false,
            errorMessage: '',
            successData: {},
            courseId: null,
            courseTitle: '',
            coursePrice: '0.00',
            courseRawPrice: 0,
            courseBatch: '',
            courseVenue: 'Online Live (Zoom)',
            courseWhatsapp: '{{ $drawerWaDigits }}',
            attendanceMode: 'online', // 'online' | 'in_person' | 'hybrid'
            isOnlineOnly: true,
            isCampusOnly: false,
            copied: false,
            
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            hasSavedPhone: {{ (auth()->check() && preg_match('/^01[3-9]\d{8}$/', preg_replace('/[^0-9]/', '', (string) (auth()->user()->phone ?? '')))) ? 'true' : 'false' }},
            name: '{{ auth()->check() ? addslashes(auth()->user()->name) : '' }}',
            phone: '{{ auth()->check() ? addslashes(auth()->user()->phone ?? '') : '' }}',
            email: '{{ auth()->check() ? addslashes(auth()->user()->email) : '' }}',
            password: '',
            showPassword: false,
            trainingMode: 'online_zoom',
            paymentMethodId: 2,
            senderNumber: '{{ auth()->check() ? addslashes(auth()->user()->phone ?? '') : '' }}',
            transactionId: '',
            fields: [],
            customFields: {},
            isEmailEnabled: true,
            isEmailRequired: true,

            resolveAttendanceMode(venueStr, explicitMode) {
                if (explicitMode && ['online', 'in_person', 'hybrid'].includes(explicitMode)) {
                    return explicitMode;
                }
                const v = (venueStr || '').toString().toLowerCase().trim();
                if (!v) return 'online';
                if (v.includes('hybrid') || v.includes('both')) return 'hybrid';
                const hasOnline = /(online|zoom|virtual|webinar|meet)/i.test(v);
                const hasCampus = /(campus|in-person|in\s+person|offline|classroom|training\s+room|mirpur|pallabi|banani|uttara|physical)/i.test(v);
                if (hasOnline && hasCampus) return 'hybrid';
                if (hasCampus && !hasOnline) return 'in_person';
                return 'online';
            },

            open(data) {
                this.courseId = data.id;
                this.courseTitle = data.title;
                this.coursePrice = data.price;
                this.courseRawPrice = data.rawPrice || 0;
                this.courseBatch = data.batch || '';
                this.courseVenue = data.venue || 'Online Live (Zoom)';
                this.courseWhatsapp = data.whatsapp || '{{ $drawerWaDigits }}';

                // Determine whether course is Online-Only, Campus-Only, or Hybrid
                this.attendanceMode = this.resolveAttendanceMode(this.courseVenue, data.attendanceMode);
                this.isOnlineOnly = (this.attendanceMode === 'online');
                this.isCampusOnly = (this.attendanceMode === 'in_person');

                if (this.isCampusOnly) {
                    this.trainingMode = 'in_person';
                } else {
                    this.trainingMode = 'online_zoom';
                }
                
                // Parse dynamic form fields configured for this course
                this.fields = Array.isArray(data.fields) && data.fields.length > 0 ? data.fields : [];
                
                // Determine email requirement from configured course fields
                const emailField = this.fields.find(f => f.name === 'email');
                if (emailField) {
                    this.isEmailEnabled = emailField.is_enabled !== false;
                    this.isEmailRequired = !!emailField.is_required;
                } else if (this.fields.length > 0) {
                    this.isEmailEnabled = false;
                    this.isEmailRequired = false;
                } else {
                    this.isEmailEnabled = true;
                    this.isEmailRequired = true;
                }

                // Initialize custom fields state
                this.customFields = {};
                this.fields.forEach(f => {
                    if (!['name', 'phone', 'email', 'training_mode'].includes(f.name)) {
                        this.customFields[f.name] = '';
                    }
                });

                this.step = 1;
                this.isSuccess = false;
                this.errorMessage = '';
                this.isOpen = true;
                document.body.style.overflow = 'hidden';
            },
            fieldLabel(name, fallback) {
                const f = this.fields.find(x => x.name === name);
                return (f && f.label) ? f.label : fallback;
            },
            fieldPlaceholder(name, fallback) {
                const f = this.fields.find(x => x.name === name);
                return (f && f.placeholder) ? f.placeholder : fallback;
            },
            isTrainingModeEnabled() {
                const f = this.fields.find(x => x.name === 'training_mode');
                return f ? f.is_enabled !== false : true;
            },
            fieldOptions(field) {
                if (!field || !field.options) return [];
                if (Array.isArray(field.options)) return field.options;
                if (typeof field.options === 'string') {
                    return field.options.split(/[,;\n]+/).map(s => s.trim()).filter(Boolean);
                }
                if (typeof field.options === 'object') {
                    return Object.values(field.options);
                }
                return [];
            },
            selectTrainingMode(mode) {
                if (mode === 'in_person' && this.isOnlineOnly) {
                    return;
                }
                if (mode === 'online_zoom' && this.isCampusOnly) {
                    return;
                }
                this.trainingMode = mode;
            },
            close() {
                this.isOpen = false;
                document.body.style.overflow = 'auto';
            },
            copyBkash() {
                navigator.clipboard.writeText('{{ $drawerMerchantDigits }}');
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            },
            async submitEnrollment() {
                this.errorMessage = '';
                if (!this.isLoggedIn && !this.name.trim()) {
                    this.errorMessage = 'Please enter your full name.';
                    return;
                }

                // Resolve phone from profile/input or senderNumber for logged-in users
                let candidatePhone = this.phone || (this.isLoggedIn ? this.senderNumber : '');
                const cleanPhone = (candidatePhone || '').replace(/[^0-9]/g, '');
                if (!cleanPhone || !/^01[3-9]\d{8}$/.test(cleanPhone)) {
                    this.errorMessage = 'Please enter a valid 11-digit mobile number (e.g., 017xxxxxxxx).';
                    return;
                }

                // Validate email if guest and enabled + required
                if (!this.isLoggedIn && this.isEmailEnabled && this.isEmailRequired) {
                    if (!this.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim())) {
                        this.errorMessage = 'Please enter a valid email address.';
                        return;
                    }
                }

                // Validate login password / PIN (at least 4 digits/chars) for guest users
                if (!this.isLoggedIn) {
                    if (!this.password || this.password.trim().length < 4) {
                        this.errorMessage = 'Please set a login password or PIN (at least 4 digits/characters) so you can log in later.';
                        return;
                    }
                }

                // Validate custom fields marked required
                for (const f of this.fields) {
                    if (!['name', 'phone', 'email', 'training_mode'].includes(f.name) && f.is_enabled && f.is_required) {
                        const val = this.customFields[f.name];
                        if (!val || !val.toString().trim()) {
                            this.errorMessage = 'Please provide ' + (f.label || f.name) + '.';
                            return;
                        }
                    }
                }

                // Strict payment validation for paid courses (courseRawPrice > 0)
                const cleanSender = ((this.senderNumber || '').trim() || cleanPhone).replace(/[^0-9]/g, '');
                if (this.courseRawPrice > 0) {
                    if (!cleanSender || !/^01[3-9]\d{8}$/.test(cleanSender)) {
                        this.errorMessage = 'Please enter the 11-digit Sender Mobile Number used for payment.';
                        return;
                    }
                    const cleanTrx = (this.transactionId || '').trim();
                    if (!cleanTrx || cleanTrx.length < 4 || ['RESERVED-OFFLINE', 'FREE'].includes(cleanTrx.toUpperCase())) {
                        this.errorMessage = 'Payment is required for this course. Please enter your valid bKash/Nagad Transaction ID (TrxID).';
                        return;
                    }
                }

                // Enforce course attendance lock prior to submission
                const effectiveMode = this.isOnlineOnly ? 'online_zoom' : (this.isCampusOnly ? 'in_person' : this.trainingMode);

                this.isSubmitting = true;

                try {
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
                    const response = await fetch(`/courses/${this.courseId}/express-enroll`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            name: this.name,
                            phone: cleanPhone,
                            email: this.isLoggedIn ? this.email : (this.isEmailEnabled ? this.email : ''),
                            password: this.password,
                            training_mode: effectiveMode,
                            custom_fields: this.customFields,
                            payment_method_id: this.courseRawPrice > 0 ? this.paymentMethodId : null,
                            sender_number: this.courseRawPrice > 0 ? cleanSender : cleanPhone,
                            transaction_id: this.courseRawPrice > 0 ? this.transactionId.trim() : null
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Enrollment failed. Please check your information.');
                    }

                    this.isSuccess = true;
                    this.successData = data;
                } catch (err) {
                    this.errorMessage = err.message || 'An error occurred during enrollment. Please try again.';
                } finally {
                    this.isSubmitting = false;
                }
            }
        }));
    }

    if (window.Alpine) {
        initDrawer();
    } else {
        document.addEventListener('alpine:init', initDrawer);
    }
})();
</script>

<div 
    x-data="expressEnrollDrawer()"
    @open-enroll-drawer.window="open($event.detail)"
    @keydown.escape.window="close()"
    x-cloak
>
    <!-- Dark Frosted Backdrop -->
    <div 
        x-show="isOpen" 
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-[#020b1c]/65 backdrop-blur-md z-40"
        @click="close()"
        style="display: none;"
    ></div>

    <!-- Slide-Over Drawer Container -->
    <div 
        id="express-enroll-drawer"
        x-show="isOpen" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 max-w-full w-full sm:max-w-[500px] bg-[#f8fafc] shadow-[-20px_0_60px_rgba(2,11,28,0.35)] z-50 flex flex-col justify-between overflow-hidden border-l border-white/20"
        style="display: none;"
    >
        <!-- 1. Upgraded Luxury Drawer Header (Deep Imperial Navy with Ambient Glow & Glass Investment Bar) -->
        <div class="px-6 pt-5 pb-5 bg-gradient-to-br from-[#031029] via-[#051c45] to-[#0b2d6b] text-white relative overflow-hidden shrink-0">
            <!-- Subtle Ambient Radial Highlights -->
            <div class="absolute -top-16 -right-16 w-44 h-44 rounded-full bg-[#c30102]/25 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-44 h-44 rounded-full bg-emerald-500/15 blur-2xl pointer-events-none"></div>

            <button 
                @click="close()" 
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 border border-white/15 text-white flex items-center justify-center transition-all hover:rotate-90 z-10 cursor-pointer"
                aria-label="Close"
            >
                <i class="fas fa-times text-xs"></i>
            </button>

            <div class="relative z-10 flex items-center flex-wrap gap-1.5 mb-2.5 pr-8">
                <template x-if="courseBatch">
                    <span class="px-2.5 py-0.5 rounded-full bg-gradient-to-r from-[#c30102] to-[#e11d48] text-white text-[10px] font-black tracking-wider uppercase shadow-sm">
                        <span x-text="courseBatch"></span>
                    </span>
                </template>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-[#34d399] text-[10px] font-bold flex items-center gap-1">
                    <i class="fas fa-bolt text-[9px]"></i>
                    <span>30-Second Express Enroll</span>
                </span>
                <span class="px-2.5 py-0.5 rounded-full bg-white/10 border border-white/15 text-sky-200 text-[10px] font-semibold flex items-center gap-1">
                    <i class="fas" :class="isOnlineOnly ? 'fa-video text-sky-300' : (isCampusOnly ? 'fa-building text-amber-300' : 'fa-layer-group text-emerald-300')"></i>
                    <span x-text="isOnlineOnly ? 'Online Live (Zoom)' : (isCampusOnly ? 'Executive Campus' : 'Hybrid Format')"></span>
                </span>
            </div>

            <h3 class="relative z-10 text-base sm:text-lg font-extrabold leading-snug text-white pr-6 line-clamp-2 tracking-tight" x-text="courseTitle"></h3>

            <!-- Glass Investment Summary Bar -->
            <div class="relative z-10 mt-3.5 px-3.5 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/15 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-emerald-300 text-xs">
                        <i class="fas fa-shield-check"></i>
                    </span>
                    <div>
                        <span class="block text-[10px] uppercase tracking-wider text-slate-300 font-bold">Total Investment</span>
                        <span class="block text-[10px] text-emerald-300 font-medium">Instant Portal &amp; Seat Activation</span>
                    </div>
                </div>
                <span class="text-lg sm:text-xl font-black text-white tracking-tight" x-text="courseRawPrice > 0 ? (coursePrice + ' ৳') : 'Free Admission'"></span>
            </div>
        </div>

        <!-- 2. Drawer Body (Form or Success Screen) -->
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
            
            <!-- Error Alert -->
            <div x-show="errorMessage" class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-semibold flex items-center gap-2.5 shadow-xs" style="display: none;">
                <i class="fas fa-exclamation-circle text-[#c30102] text-sm shrink-0"></i>
                <span x-text="errorMessage"></span>
            </div>

            <!-- SUCCESS CONFIRMATION STATE -->
            <div x-show="isSuccess" class="text-center py-6 space-y-4 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs" style="display: none;">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-3xl mx-auto shadow-inner">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h4 class="text-xl font-black text-[#0f131f]">Seat Reserved Successfully!</h4>
                    <p class="text-xs text-[#727f9f] max-w-sm mx-auto leading-relaxed mt-1" x-text="successData.message"></p>
                </div>

                <div class="p-4 bg-[#f8fafc] rounded-2xl border border-slate-200/80 text-xs text-left space-y-2 max-w-sm mx-auto">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Student Name:</span>
                        <span class="font-bold text-[#0f131f]" x-text="name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Mobile:</span>
                        <span class="font-bold text-[#0f131f]" x-text="phone"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Attendance Mode:</span>
                        <span class="font-bold text-[#051c45]" x-text="trainingMode === 'in_person' ? 'Executive Campus (In-Person)' : 'Live Online (Zoom)'"></span>
                    </div>
                    <template x-for="(val, key) in customFields" :key="key">
                        <template x-if="val && val.toString().trim()">
                            <div class="flex justify-between">
                                <span class="text-gray-500 capitalize" x-text="key.replace('_', ' ') + ':'"></span>
                                <span class="font-bold text-[#0f131f]" x-text="val"></span>
                            </div>
                        </template>
                    </template>
                    <template x-if="successData.transaction_id">
                        <div class="flex justify-between border-t border-gray-200 pt-2">
                            <span class="text-gray-500">Reference TrxID:</span>
                            <span class="font-mono font-bold text-[#c30102]" x-text="successData.transaction_id"></span>
                        </div>
                    </template>
                </div>

                <!-- Student Portal Login Credentials Card -->
                <div class="p-4 bg-[#051c45]/5 rounded-2xl border border-[#051c45]/15 text-xs text-left space-y-2 max-w-sm mx-auto">
                    <div class="flex items-center gap-1.5 font-black text-[#051c45] uppercase tracking-wider text-[11px]">
                        <i class="fas fa-key text-[#c30102]"></i>
                        <span>Your Student Portal Login Credentials</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Login ID (Mobile / Email):</span>
                        <span class="font-mono font-bold text-[#051c45]" x-text="successData.login_id || phone"></span>
                    </div>
                    <div class="flex justify-between" x-show="!isLoggedIn && password">
                        <span class="text-gray-600">Password / PIN:</span>
                        <span class="font-mono font-bold text-[#10b981]" x-text="password"></span>
                    </div>
                    <p class="text-[10px] text-gray-500 leading-snug pt-1 border-t border-[#051c45]/10">
                        Use your mobile number (or email) and this password/PIN at <span class="font-semibold text-[#051c45]">/login</span> anytime to access your classroom &amp; certificates.
                    </p>
                </div>

                <div class="pt-2 flex flex-col gap-2">
                    <a 
                        :href="successData.redirect_url || '{{ route('dashboard') }}'" 
                        class="btn-ipdp-primary w-full py-3 rounded-xl font-bold text-sm text-center shadow-lg"
                    >
                        Go to Classroom / Dashboard
                    </a>
                    <button 
                        @click="close()" 
                        class="text-xs text-gray-500 hover:text-gray-800 py-1 cursor-pointer"
                    >
                        Close Window
                    </button>
                </div>
            </div>

            <!-- ENROLLMENT FORM STATE -->
            <div x-show="!isSuccess" class="space-y-4">
                
                <!-- Step 1 Card: Student Information & Attendance Format -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-[0_2px_12px_rgba(5,28,69,0.04)] space-y-3.5">
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-[#051c45] flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-[#051c45] text-white text-[10px] font-black flex items-center justify-center shadow-xs">1</span>
                            <span x-text="isLoggedIn ? 'Verified Student Account' : 'Student Information'"></span>
                        </label>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-100 text-slate-500" x-text="courseRawPrice > 0 ? 'Step 1 of 2' : 'Step 1 of 1 • Free Admission'"></span>
                    </div>

                    <!-- Logged-in Student Verified Profile Summary (No need to re-type Name, Email, or Password) -->
                    <template x-if="isLoggedIn">
                        <div class="space-y-3">
                            <div class="p-3.5 rounded-xl bg-gradient-to-br from-[#051c45]/[0.04] to-emerald-500/[0.06] border border-[#051c45]/15 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-[#051c45] text-white font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                                        <span x-text="name ? name.charAt(0).toUpperCase() : 'S'"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="text-xs font-extrabold text-[#051c45] truncate" x-text="name"></p>
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[9px] font-black uppercase tracking-wider shrink-0">
                                                <i class="fas fa-check-circle text-[8px]"></i> Verified
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 truncate mt-0.5" x-text="email"></p>
                                        <template x-if="hasSavedPhone">
                                            <p class="text-[10px] font-mono font-bold text-slate-600 mt-0.5">
                                                <i class="fas fa-mobile-alt text-[#10b981] mr-1"></i><span x-text="phone"></span>
                                            </p>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Only ask for Mobile Number if not yet saved on the logged-in user's profile -->
                            <template x-if="!hasSavedPhone">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Mobile Number (11 Digits) <span class="text-[#c30102]">*</span></label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                            <i class="fas fa-mobile-alt text-xs"></i>
                                        </span>
                                        <input 
                                            type="tel" 
                                            x-model="phone"
                                            @input="if (!senderNumber || senderNumber.length < 11) senderNumber = phone"
                                            placeholder="017xxxxxxxx" 
                                            class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-medium text-[#0f131f] focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:bg-white focus:outline-none transition-all"
                                            required
                                        >
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Guest Student Inputs (Full Name, Mobile, Email, & Login PIN) -->
                    <template x-if="!isLoggedIn">
                        <div class="space-y-3.5">
                            <!-- Full Name -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1"><span x-text="fieldLabel('name', 'Full Name')">Full Name</span> <span class="text-[#c30102]">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                        <i class="far fa-user text-xs"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        x-model="name"
                                        :placeholder="fieldPlaceholder('name', 'e.g. Tanvir Ahmed')"
                                        placeholder="e.g. Tanvir Ahmed" 
                                        class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-medium text-[#0f131f] focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:bg-white focus:outline-none transition-all"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Mobile & Email Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div :class="isEmailEnabled ? '' : 'sm:col-span-2'">
                                    <label class="block text-xs font-bold text-slate-700 mb-1"><span x-text="fieldLabel('phone', 'Mobile (11 Digits)')">Mobile (11 Digits)</span> <span class="text-[#c30102]">*</span></label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                            <i class="fas fa-mobile-alt text-xs"></i>
                                        </span>
                                        <input 
                                            type="tel" 
                                            x-model="phone"
                                            @input="if (!senderNumber || senderNumber.length < 11) senderNumber = phone"
                                            :placeholder="fieldPlaceholder('phone', '017xxxxxxxx')"
                                            placeholder="017xxxxxxxx" 
                                            class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-medium text-[#0f131f] focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:bg-white focus:outline-none transition-all"
                                            required
                                        >
                                    </div>
                                </div>
                                <template x-if="isEmailEnabled">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            <span x-text="fieldLabel('email', 'Email Address')">Email Address</span>
                                            <span x-show="isEmailRequired" class="text-[#c30102] font-bold">*</span>
                                            <span x-show="!isEmailRequired" class="text-slate-400 font-normal text-[10px]">(Optional)</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                                <i class="far fa-envelope text-xs"></i>
                                            </span>
                                            <input 
                                                type="email" 
                                                x-model="email"
                                                :placeholder="fieldPlaceholder('email', 'name@company.com')"
                                                placeholder="name@company.com" 
                                                class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-medium text-[#0f131f] focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:bg-white focus:outline-none transition-all"
                                                :required="isEmailRequired"
                                            >
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Set Login Password / PIN (At least 4 Digits/Chars) for Returning Login -->
                            <div class="p-3.5 bg-gradient-to-br from-[#051c45]/[0.03] to-[#051c45]/[0.07] border border-[#051c45]/15 rounded-xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-extrabold text-[#051c45] flex items-center gap-1.5">
                                        <i class="fas fa-lock text-[#c30102] text-[11px]"></i>
                                        <span>Set Login Password / PIN (Min 4 Digits)</span>
                                        <span class="text-[#c30102] font-bold">*</span>
                                    </label>
                                    <button type="button" @click="showPassword = !showPassword" class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-[10px] font-bold text-[#051c45] hover:bg-slate-50 transition-colors cursor-pointer">
                                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                        <i class="fas fa-key text-xs"></i>
                                    </span>
                                    <input 
                                        :type="showPassword ? 'text' : 'password'" 
                                        x-model="password"
                                        minlength="4"
                                        placeholder="Enter at least 4 digits or characters (e.g. 1234)" 
                                        class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-xs text-[#0f131f] font-mono focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:outline-none transition-all"
                                        required
                                    >
                                </div>
                                <p class="text-[10px] text-slate-600 leading-snug flex items-center gap-1.5">
                                    <i class="fas fa-info-circle text-[#051c45] text-[10px] shrink-0"></i>
                                    <span>Use your <strong>11-digit mobile number (or email)</strong> + this PIN to log in anytime from any device.</span>
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- Dynamic Custom Fields Configured for this Course -->
                    <template x-for="field in fields.filter(f => !['name', 'phone', 'email', 'training_mode'].includes(f.name) && (f.is_enabled !== false))" :key="field.name">
                        <div class="pt-0.5">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                <span x-text="field.label || field.name"></span>
                                <span x-show="field.is_required" class="text-[#c30102] font-bold">*</span>
                                <span x-show="!field.is_required" class="text-slate-400 font-normal text-[10px]">(Optional)</span>
                            </label>

                            <!-- Multi-line Textarea -->
                            <template x-if="field.type === 'textarea'">
                                <textarea 
                                    x-model="customFields[field.name]"
                                    :placeholder="field.placeholder || ''"
                                    rows="2"
                                    class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-[#0f131f] focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:bg-white focus:outline-none transition-all"
                                ></textarea>
                            </template>

                            <!-- Dropdown Select -->
                            <template x-if="field.type === 'select'">
                                <select
                                    x-model="customFields[field.name]"
                                    class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-[#0f131f] focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:bg-white focus:outline-none transition-all"
                                >
                                    <option value="" x-text="field.placeholder || ('Select ' + (field.label || field.name))"></option>
                                    <template x-for="opt in fieldOptions(field)" :key="opt">
                                        <option :value="opt" x-text="opt"></option>
                                    </template>
                                </select>
                            </template>

                            <!-- 1-Tap Choice Pills (Radio) -->
                            <template x-if="field.type === 'radio'">
                                <div class="grid grid-cols-2 gap-2">
                                    <template x-for="opt in fieldOptions(field)" :key="opt">
                                        <button
                                            type="button"
                                            @click="customFields[field.name] = opt"
                                            class="px-3 py-2 rounded-xl border text-left text-[11px] font-bold transition-all flex items-center justify-between gap-1.5 cursor-pointer"
                                            :class="customFields[field.name] === opt
                                                ? 'border-[#051c45] bg-[#051c45] text-white shadow-xs'
                                                : 'border-slate-200 bg-[#f8fafc] hover:bg-white hover:border-slate-300 text-slate-700'"
                                        >
                                            <span class="truncate" x-text="opt"></span>
                                            <i class="fas fa-check-circle text-[10px] shrink-0" :class="customFields[field.name] === opt ? 'text-[#10b981]' : 'text-transparent'"></i>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <!-- Checkbox Toggle -->
                            <template x-if="field.type === 'checkbox'">
                                <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-[#f8fafc] hover:bg-white cursor-pointer transition-all">
                                    <input type="checkbox" x-model="customFields[field.name]" true-value="Yes" false-value="" class="rounded border-slate-300 text-[#051c45] focus:ring-[#051c45]">
                                    <span class="text-xs font-medium text-slate-700" x-text="field.placeholder || field.label"></span>
                                </label>
                            </template>

                            <!-- Standard Input (text, tel, email, number, date, url) -->
                            <template x-if="!['textarea', 'select', 'radio', 'checkbox'].includes(field.type)">
                                <input 
                                    :type="field.type || 'text'" 
                                    x-model="customFields[field.name]"
                                    :placeholder="field.placeholder || ''"
                                    class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-medium text-[#0f131f] focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:bg-white focus:outline-none transition-all"
                                >
                            </template>
                        </div>
                    </template>

                    <!-- Upgraded Preferred Attendance Mode Selector (Auto-disables Executive Campus when Course is Online) -->
                    <div class="pt-1" x-show="isTrainingModeEnabled()">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700" x-text="fieldLabel('training_mode', 'Preferred Attendance Mode')">Preferred Attendance Mode</label>
                            <template x-if="isOnlineOnly">
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>Online Live Program</span>
                                </span>
                            </template>
                            <template x-if="isCampusOnly">
                                <span class="text-[10px] font-bold text-[#051c45] bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <i class="fas fa-building text-[9px]"></i>
                                    <span>Campus-Only Program</span>
                                </span>
                            </template>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 text-xs">
                            <!-- Option 1: Live Online (Zoom) -->
                            <button
                                type="button"
                                @click="selectTrainingMode('online_zoom')"
                                :disabled="isCampusOnly"
                                :aria-disabled="isCampusOnly ? 'true' : 'false'"
                                class="relative text-left p-3 rounded-xl border transition-all flex items-start gap-2.5"
                                :class="isCampusOnly
                                    ? 'bg-slate-100/80 border-slate-200 text-slate-400 opacity-55 cursor-not-allowed select-none'
                                    : (trainingMode === 'online_zoom'
                                        ? 'border-[#051c45] bg-[#051c45]/[0.05] ring-2 ring-[#051c45]/15 text-[#051c45] shadow-xs cursor-pointer'
                                        : 'border-slate-200 bg-white hover:border-slate-300 text-slate-700 cursor-pointer')"
                            >
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5"
                                     :class="isCampusOnly ? 'bg-slate-200 text-slate-400' : (trainingMode === 'online_zoom' ? 'bg-[#051c45] text-white' : 'bg-sky-50 text-sky-600')">
                                    <i class="fas fa-video text-xs"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-extrabold text-xs leading-tight flex items-center justify-between gap-1">
                                        <span class="truncate">Live Online (Zoom)</span>
                                        <template x-if="trainingMode === 'online_zoom' && !isCampusOnly">
                                            <i class="fas fa-check-circle text-[#10b981] text-xs shrink-0"></i>
                                        </template>
                                    </div>
                                    <p class="text-[10px] mt-0.5 leading-tight"
                                       :class="isCampusOnly ? 'text-slate-400' : 'text-slate-500'"
                                       x-text="isCampusOnly ? 'Unavailable (Campus Only)' : 'HD Live + Recordings'"></p>
                                </div>
                            </button>

                            <!-- Option 2: Executive Campus (Automatically Disabled when Course is Set Online) -->
                            <button
                                type="button"
                                @click="selectTrainingMode('in_person')"
                                :disabled="isOnlineOnly"
                                :aria-disabled="isOnlineOnly ? 'true' : 'false'"
                                class="relative text-left p-3 rounded-xl border transition-all flex items-start gap-2.5"
                                :class="isOnlineOnly
                                    ? 'bg-slate-100/80 border-slate-200/90 text-slate-400 opacity-60 cursor-not-allowed select-none'
                                    : (trainingMode === 'in_person'
                                        ? 'border-[#051c45] bg-[#051c45]/[0.05] ring-2 ring-[#051c45]/15 text-[#051c45] shadow-xs cursor-pointer'
                                        : 'border-slate-200 bg-white hover:border-slate-300 text-slate-700 cursor-pointer')"
                            >
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5"
                                     :class="isOnlineOnly ? 'bg-slate-200/80 text-slate-400' : (trainingMode === 'in_person' ? 'bg-[#051c45] text-white' : 'bg-slate-100 text-slate-600')">
                                    <i class="fas" :class="isOnlineOnly ? 'fa-lock text-xs' : 'fa-building text-xs'"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-extrabold text-xs leading-tight flex items-center justify-between gap-1">
                                        <span class="truncate" :class="isOnlineOnly ? 'line-through decoration-slate-300' : ''">Executive Campus</span>
                                        <template x-if="isOnlineOnly">
                                            <span class="px-1.5 py-0.2 rounded bg-slate-200 text-slate-600 text-[8px] font-black uppercase tracking-wider shrink-0">Disabled</span>
                                        </template>
                                        <template x-if="trainingMode === 'in_person' && !isOnlineOnly">
                                            <i class="fas fa-check-circle text-[#10b981] text-xs shrink-0"></i>
                                        </template>
                                    </div>
                                    <p class="text-[10px] mt-0.5 leading-tight"
                                       :class="isOnlineOnly ? 'text-slate-400 font-medium' : 'text-slate-500'"
                                       x-text="isOnlineOnly ? 'Online-Only Course' : 'In-Person Classroom'"></p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2 Card: Payment or Seat Reservation (Only shown if course is paid) -->
                <template x-if="courseRawPrice > 0">
                    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-[0_2px_12px_rgba(5,28,69,0.04)] space-y-3.5">
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                            <label class="text-xs font-extrabold uppercase tracking-wider text-[#051c45] flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-[#051c45] text-white text-[10px] font-black flex items-center justify-center shadow-xs">2</span>
                                <span>Payment &amp; Confirmation</span>
                            </label>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">Step 2 of 2</span>
                        </div>

                        <div class="space-y-3">
                            <!-- Upgraded bKash Merchant Card -->
                            <div class="p-3.5 rounded-2xl border border-pink-200/90 bg-gradient-to-br from-pink-50/70 via-white to-pink-50/40 space-y-2.5 shadow-2xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-[#e2136e] text-white flex items-center justify-center text-[11px] font-black shadow-xs">b</span>
                                        <span class="font-black text-[#e2136e] text-sm tracking-tight">bKash / Nagad</span>
                                        <span class="text-[9px] bg-[#e2136e]/10 text-[#e2136e] border border-[#e2136e]/20 px-2 py-0.5 rounded-full font-extrabold uppercase tracking-wider">Merchant / Personal</span>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click="copyBkash()"
                                        class="px-2.5 py-1 rounded-lg bg-white hover:bg-pink-50 border border-pink-200 text-[11px] font-bold text-[#e2136e] transition-all flex items-center gap-1 shadow-2xs cursor-pointer"
                                    >
                                        <i class="far" :class="copied ? 'fa-check-circle text-emerald-600' : 'fa-copy'"></i>
                                        <span x-text="copied ? 'Copied!' : 'Copy Number'"></span>
                                    </button>
                                </div>
                                <div class="text-xs font-mono font-bold text-slate-800 bg-white px-3 py-2 rounded-xl border border-pink-100 flex items-center justify-between">
                                    <span>Account Number: <strong class="text-[#051c45]">{{ $drawerMerchantAccount }}</strong></span>
                                    <span class="text-[10px] text-[#e2136e] font-sans font-bold">Instant Verify</span>
                                </div>
                            </div>

                            <!-- TrxID and Sender Input (Required for all paid courses) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Sender Mobile Number <span class="text-[#c30102]">*</span></label>
                                    <input 
                                        type="tel" 
                                        x-model="senderNumber"
                                        placeholder="017xxxxxxxx" 
                                        class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-[#0f131f] focus:outline-none focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:bg-white"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Transaction ID (TrxID) <span class="text-[#c30102]">*</span></label>
                                    <input 
                                        type="text" 
                                        x-model="transactionId"
                                        placeholder="e.g. 9J283KA9Q" 
                                        class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-3 py-2 text-xs text-[#0f131f] font-mono uppercase focus:outline-none focus:ring-2 focus:ring-[#051c45]/20 focus:border-[#051c45] focus:bg-white"
                                        required
                                    >
                                </div>
                            </div>

                            <p class="text-[10px] text-slate-500 flex items-center gap-1.5 px-1">
                                <i class="fas fa-shield-check text-emerald-600 text-[10px]"></i>
                                <span>Send the exact course fee to the number above and enter your <strong>Sender Number</strong> &amp; <strong>TrxID</strong> to complete enrollment.</span>
                            </p>
                        </div>
                    </div>
                </template>

                <!-- WhatsApp Alternative 1-Tap Link -->
                <div class="pt-1 text-center">
                    <a 
                        :href="'https://wa.me/' + courseWhatsapp + '?text=' + encodeURIComponent('Assalamu Alaikum! I would like to inquire/enroll in *' + courseTitle + '* (Fee: ৳' + courseRawPrice + '). Please share batch details and enrollment procedure: ' + window.location.origin + '/c/' + courseId)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 text-xs font-bold text-emerald-700 transition-colors"
                    >
                        <i class="fab fa-whatsapp text-sm text-[#10b981]"></i>
                        <span>Need help? 1-Tap WhatsApp Admission Desk</span>
                    </a>
                </div>

            </div>

        </div>

        <!-- 3. Drawer Footer Actions -->
        <div x-show="!isSuccess" class="px-6 py-4 bg-white border-t border-slate-200/90 shrink-0 shadow-[0_-8px_24px_rgba(5,28,69,0.04)]">
            <button 
                type="button" 
                @click="submitEnrollment()" 
                :disabled="isSubmitting"
                class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-[#c30102] via-[#dc0405] to-[#a50102] hover:from-[#a50102] hover:to-[#8b0001] text-white font-black text-xs sm:text-sm uppercase tracking-wider shadow-[0_8px_24px_rgba(195,1,2,0.35)] hover:shadow-[0_12px_28px_rgba(195,1,2,0.45)] transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-60 cursor-pointer active:scale-[0.99]"
            >
                <template x-if="isSubmitting">
                    <i class="fas fa-spinner fa-spin text-base"></i>
                </template>
                <template x-if="!isSubmitting">
                    <i class="fas fa-arrow-right text-xs"></i>
                </template>
                <span x-text="isSubmitting ? 'Securing Your Seat...' : (courseRawPrice > 0 ? 'Complete Express Enrollment' : 'Enroll in Free Course')"></span>
            </button>
            <p class="text-[10px] text-center text-slate-400 font-medium mt-2 flex items-center justify-center gap-1.5">
                <i class="fas fa-lock text-[#10b981] text-[9px]"></i>
                <span x-text="isOnlineOnly ? '256-Bit Encrypted • Instant Classroom Access • Verified Certification' : '256-Bit Encrypted • Direct Verified Admission • Executive Campus & Online'"></span>
            </p>
        </div>

    </div>
</div>
