<!-- Express Course Enrollment Drawer (30-Second Rapid Conversion Engine) -->
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
            courseBatch: '105th BATCH',
            courseVenue: 'Mirpur-Pallabi Campus & Online Zoom',
            copied: false,
            
            name: '{{ auth()->check() ? addslashes(auth()->user()->name) : '' }}',
            phone: '',
            email: '{{ auth()->check() ? addslashes(auth()->user()->email) : '' }}',
            trainingMode: 'in_person',
            paymentMethodId: 2,
            senderNumber: '',
            transactionId: '',
            payOffline: false,

            open(data) {
                this.courseId = data.id;
                this.courseTitle = data.title;
                this.coursePrice = data.price;
                this.courseRawPrice = data.rawPrice || 0;
                this.courseBatch = data.batch || '105th BATCH';
                this.courseVenue = data.venue || 'Mirpur-Pallabi Campus & Online Zoom';
                this.step = 1;
                this.isSuccess = false;
                this.errorMessage = '';
                this.isOpen = true;
                document.body.style.overflow = 'hidden';
            },
            close() {
                this.isOpen = false;
                document.body.style.overflow = 'auto';
            },
            copyBkash() {
                navigator.clipboard.writeText('01917054664');
                this.copied = true;
                setTimeout(() => this.copied = false, 2500);
            },
            async submitEnrollment() {
                this.errorMessage = '';
                if (!this.name.trim()) {
                    this.errorMessage = 'Please enter your full name.';
                    return;
                }
                const cleanPhone = this.phone.replace(/[^0-9]/g, '');
                if (!cleanPhone || !/^01[3-9]\d{8}$/.test(cleanPhone)) {
                    this.errorMessage = 'Please enter a valid 11-digit mobile number (e.g., 01917054664).';
                    return;
                }
                if (this.courseRawPrice > 0 && !this.payOffline && !this.transactionId.trim()) {
                    this.errorMessage = 'Please enter the payment TrxID or select Pay at Campus / Reserve.';
                    return;
                }

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
                            email: this.email,
                            training_mode: this.trainingMode,
                            payment_method_id: this.payOffline ? null : this.paymentMethodId,
                            sender_number: this.senderNumber || cleanPhone,
                            transaction_id: this.payOffline ? 'RESERVED-OFFLINE' : this.transactionId
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
    <!-- Dark Backdrop with Imperial Navy tone -->
    <div 
        x-show="isOpen" 
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-[#051c45]/60 backdrop-blur-sm z-40"
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
        class="fixed inset-y-0 right-0 max-w-full w-full sm:max-w-lg bg-white shadow-2xl z-50 flex flex-col justify-between overflow-hidden border-l border-[#e3e7f0]"
        style="display: none;"
    >
        <!-- 1. Drawer Header (Imperial Navy with Crimson Scarcity Pill) -->
        <div class="p-6 bg-[#051c45] text-white relative">
            <button 
                @click="close()" 
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                aria-label="Close"
            >
                <i class="fas fa-times text-sm"></i>
            </button>

            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded bg-[#c30102] text-white text-[10px] font-black tracking-wider uppercase shadow">
                    <span x-text="courseBatch"></span>
                </span>
                <span class="text-xs text-[#90c642] font-semibold flex items-center gap-1">
                    <i class="fas fa-bolt text-xs"></i> 30-Second Express Enroll
                </span>
            </div>

            <h3 class="text-lg sm:text-xl font-bold leading-snug text-white pr-8 line-clamp-2" x-text="courseTitle"></h3>

            <div class="mt-3 flex items-baseline justify-between pt-3 border-t border-white/10 text-xs">
                <span class="text-gray-300">Total Investment:</span>
                <span class="text-xl font-black text-white" x-text="courseRawPrice > 0 ? (coursePrice + ' ৳') : 'Free Admission'"></span>
            </div>
        </div>

        <!-- 2. Drawer Body (Form or Success Screen) -->
        <div class="flex-1 overflow-y-auto p-6 space-y-5">
            
            <!-- Error Alert -->
            <div x-show="errorMessage" class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-medium flex items-center gap-2" style="display: none;">
                <i class="fas fa-exclamation-circle text-red-500 text-sm shrink-0"></i>
                <span x-text="errorMessage"></span>
            </div>

            <!-- SUCCESS CONFIRMATION STATE -->
            <div x-show="isSuccess" class="text-center py-8 space-y-4" style="display: none;">
                <div class="w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-3xl mx-auto shadow-inner">
                    <i class="fas fa-check"></i>
                </div>
                <h4 class="text-xl font-black text-[#0f131f]">Seat Reserved Successfully!</h4>
                <p class="text-xs text-[#727f9f] max-w-sm mx-auto leading-relaxed" x-text="successData.message"></p>

                <div class="p-4 bg-[#f8fafc] rounded-2xl border border-gray-100 text-xs text-left space-y-2 max-w-sm mx-auto">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Student Name:</span>
                        <span class="font-bold text-[#0f131f]" x-text="name"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Mobile:</span>
                        <span class="font-bold text-[#0f131f]" x-text="phone"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Format:</span>
                        <span class="font-bold text-[#0f131f]" x-text="trainingMode === 'in_person' ? 'Mirpur Campus (In-Person)' : 'Online Live (Zoom)'"></span>
                    </div>
                    <template x-if="successData.transaction_id">
                        <div class="flex justify-between border-t border-gray-200 pt-2">
                            <span class="text-gray-500">Reference TrxID:</span>
                            <span class="font-mono font-bold text-[#f15a29]" x-text="successData.transaction_id"></span>
                        </div>
                    </template>
                </div>

                <div class="pt-4 flex flex-col gap-2.5">
                    <a 
                        :href="successData.redirect_url || '{{ route('dashboard') }}'" 
                        class="btn-ipdp-primary w-full py-3 rounded-xl font-bold text-sm text-center shadow-lg"
                    >
                        Go to Classroom / Dashboard
                    </a>
                    <button 
                        @click="close()" 
                        class="text-xs text-gray-500 hover:text-gray-800 py-1"
                    >
                        Close Window
                    </button>
                </div>
            </div>

            <!-- ENROLLMENT FORM STATE -->
            <div x-show="!isSuccess" class="space-y-5">
                
                <!-- Step 1: Student Information -->
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold uppercase tracking-wider text-[#051c45] flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-[#051c45] text-white text-[11px] font-bold flex items-center justify-center">1</span>
                            Student Information
                        </label>
                        <span class="text-[11px] text-[#727f9f]">Step 1 of 2</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#35415b] mb-1">Full Name *</label>
                        <input 
                            type="text" 
                            x-model="name"
                            placeholder="e.g. Tanvir Ahmed" 
                            class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-[#0f131f] focus:ring-2 focus:ring-[#051c45] focus:bg-white focus:outline-none"
                            required
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-[#35415b] mb-1">Mobile (11 Digits) *</label>
                            <input 
                                type="tel" 
                                x-model="phone"
                                placeholder="01917054664" 
                                class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-[#0f131f] focus:ring-2 focus:ring-[#051c45] focus:bg-white focus:outline-none"
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#35415b] mb-1">Email (Optional)</label>
                            <input 
                                type="email" 
                                x-model="email"
                                placeholder="name@company.com" 
                                class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs text-[#0f131f] focus:ring-2 focus:ring-[#051c45] focus:bg-white focus:outline-none"
                            >
                        </div>
                    </div>

                    <!-- Training Format Radio Selection -->
                    <div>
                        <label class="block text-xs font-semibold text-[#35415b] mb-1.5">Preferred Attendance Mode</label>
                        <div class="grid grid-cols-2 gap-2.5 text-xs">
                            <label 
                                class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all"
                                :class="trainingMode === 'in_person' ? 'border-[#051c45] bg-[#051c45]/5 font-bold text-[#051c45]' : 'border-gray-200 text-gray-600'"
                            >
                                <input type="radio" x-model="trainingMode" value="in_person" class="hidden">
                                <i class="fas fa-building text-sm"></i>
                                <span>Mirpur Campus</span>
                            </label>
                            <label 
                                class="flex items-center gap-2 p-2.5 rounded-xl border cursor-pointer transition-all"
                                :class="trainingMode === 'online_zoom' ? 'border-[#051c45] bg-[#051c45]/5 font-bold text-[#051c45]' : 'border-gray-200 text-gray-600'"
                            >
                                <input type="radio" x-model="trainingMode" value="online_zoom" class="hidden">
                                <i class="fas fa-video text-sm text-blue-500"></i>
                                <span>Online (Zoom)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Payment or Campus Reservation (Only shown if course is paid) -->
                <template x-if="courseRawPrice > 0">
                    <div class="pt-4 border-t border-gray-100 space-y-3.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold uppercase tracking-wider text-[#051c45] flex items-center gap-1.5">
                                <span class="w-5 h-5 rounded-full bg-[#051c45] text-white text-[11px] font-bold flex items-center justify-center">2</span>
                                Payment & Confirmation
                            </label>
                            <span class="text-[11px] text-[#727f9f]">Step 2 of 2</span>
                        </div>

                        <!-- Payment Method Toggle -->
                        <div class="space-y-2">
                            <!-- bKash Merchant Box -->
                            <div class="p-3.5 rounded-2xl border border-pink-200 bg-pink-50/40 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-black text-[#e2136e] text-sm">bKash</span>
                                        <span class="text-[10px] bg-[#e2136e] text-white px-2 py-0.5 rounded font-bold uppercase">Merchant</span>
                                    </div>
                                    <button 
                                        type="button" 
                                        @click="copyBkash()"
                                        class="text-xs font-bold text-[#e2136e] hover:underline flex items-center gap-1"
                                    >
                                        <i class="far fa-copy"></i>
                                        <span x-text="copied ? 'Copied!' : 'Copy Number'"></span>
                                    </button>
                                </div>
                                <div class="text-xs font-mono font-bold text-gray-800 bg-white p-2 rounded-lg border border-pink-100 flex items-center justify-between">
                                    <span>Merchant Number: 01917-054664</span>
                                    <span class="text-[10px] text-gray-500 font-sans">Payment Option</span>
                                </div>
                            </div>

                            <!-- TrxID and Sender Input -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">bKash / Nagad Number</label>
                                    <input 
                                        type="tel" 
                                        x-model="senderNumber"
                                        placeholder="017xxxxxxxx" 
                                        class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-3 py-2 text-xs text-[#0f131f] focus:outline-none focus:ring-1 focus:ring-[#051c45]"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Transaction ID (TrxID) *</label>
                                    <input 
                                        type="text" 
                                        x-model="transactionId"
                                        placeholder="e.g. 9J283KA9Q" 
                                        class="w-full bg-[#f8fafc] border border-gray-200 rounded-xl px-3 py-2 text-xs text-[#0f131f] font-mono uppercase focus:outline-none focus:ring-1 focus:ring-[#051c45]"
                                    >
                                </div>
                            </div>

                            <!-- Pay at Campus Checkbox -->
                            <div class="pt-1">
                                <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-600 select-none">
                                    <input type="checkbox" x-model="payOffline" class="rounded border-gray-300 text-[#051c45] focus:ring-[#051c45]">
                                    <span>Pay at Campus / Confirm Payment with Counselor Later</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- WhatsApp Alternative 1-Tap Link -->
                <div class="pt-2 border-t border-gray-100 text-center">
                    <p class="text-[11px] text-gray-500 mb-2">Prefer direct phone or WhatsApp assistance?</p>
                    <a 
                        :href="'https://wa.me/8801917054664?text=' + encodeURIComponent('Hello CAB Academy, I want to reserve my seat for: ' + courseTitle + ' (' + courseBatch + '). My name is: ' + name)"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 text-xs font-bold text-[#10b981] hover:text-[#059669] hover:underline"
                    >
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>1-Tap Instant WhatsApp Reservation</span>
                    </a>
                </div>

            </div>

        </div>

        <!-- 3. Drawer Footer Actions -->
        <div x-show="!isSuccess" class="p-6 bg-[#f8fafc] border-t border-[#e3e7f0]">
            <button 
                type="button" 
                @click="submitEnrollment()" 
                :disabled="isSubmitting"
                class="w-full py-3.5 px-6 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white font-black text-sm uppercase tracking-wider shadow-xl hover:shadow-2xl transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-60"
            >
                <template x-if="isSubmitting">
                    <i class="fas fa-spinner fa-spin text-base"></i>
                </template>
                <template x-if="!isSubmitting">
                    <i class="fas fa-arrow-right text-xs"></i>
                </template>
                <span x-text="isSubmitting ? 'Securing Your Seat...' : (courseRawPrice > 0 ? 'Complete Express Enrollment' : 'Enroll in Free Course')"></span>
            </button>
            <p class="text-[10px] text-center text-gray-400 mt-2">
                🔒 256-Bit Encrypted &bull; Direct Verified Admission &bull; Mirpur-Pallabi Campus
            </p>
        </div>

    </div>
</div>
