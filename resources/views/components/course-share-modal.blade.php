<!-- Global Facebook Rich Link Preview & Stylish Caption Share Modal (Triggered from Course Cards & Course Pages) -->
<div x-data="{
        open: false,
        copiedKey: '',
        course: {
            id: null,
            title: '',
            postHeader: '',
            stylishTitle: '',
            socialCaption: '',
            shortUrl: '',
            courseUrl: '',
            image: '',
            displayDomain: 'acsacademy.bd',
            facebookShareUrl: '',
            whatsappShareUrl: ''
        },
        openModal(detail) {
            this.course = Object.assign({
                id: null,
                title: '',
                postHeader: '',
                stylishTitle: '',
                socialCaption: '',
                shortUrl: '',
                courseUrl: '',
                image: '',
                displayDomain: 'acsacademy.bd',
                facebookShareUrl: '',
                whatsappShareUrl: ''
            }, detail || {});
            this.open = true;
            this.copyText(this.course.socialCaption, 'full');
        },
        copyText(text, key) {
            if (!text) return;
            this.copiedKey = key;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).catch(() => this.fallbackCopy(text));
            } else {
                this.fallbackCopy(text);
            }
            setTimeout(() => {
                if (this.copiedKey === key) this.copiedKey = '';
            }, 2500);
        },
        fallbackCopy(text) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-999999px';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(ta);
        }
    }"
    x-on:open-course-share-modal.window="openModal($event.detail)"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    style="display: none;"
    id="globalCourseShareModal"
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
    aria-modal="true"
    role="dialog">

    <!-- Backdrop -->
    <div x-show="open"
         x-transition.opacity
         @click="open = false"
         class="fixed inset-0 bg-[#020b18]/75 backdrop-blur-xs"></div>

    <!-- Modal Card (Same Design as Admin Course Studio Module 4) -->
    <div x-show="open"
         x-transition
         @click.stop
         class="relative z-10 w-full max-w-3xl bg-white rounded-3xl border border-slate-200/90 shadow-2xl overflow-hidden my-auto">

        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 bg-gradient-to-r from-[#020b18] via-[#051c45] to-[#0a2540] text-white flex items-center justify-between gap-3">
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-0.5 rounded-full bg-[#1877F2]/25 border border-[#1877F2]/40 text-blue-200 text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1">
                        <i class="fab fa-facebook-f text-[9px]"></i>
                        <span>Facebook Rich Link Preview & Stylish Share</span>
                    </span>
                    <span x-show="copiedKey !== ''" x-transition class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-[10px] font-extrabold inline-flex items-center gap-1">
                        <i class="fas fa-check-circle text-[9px]"></i>
                        <span x-text="copiedKey === 'header' ? 'Bold Headline Copied!' : (copiedKey === 'link' ? 'Short Link Copied!' : 'Stylish Caption + Link Copied!')"></span>
                    </span>
                </div>
                <h3 class="text-sm sm:text-base font-black text-white truncate mt-1" x-text="course.title"></h3>
            </div>
            <button type="button"
                    @click="open = false"
                    class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors shrink-0 cursor-pointer"
                    aria-label="Close modal">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <!-- Body: Two-Column Layout (Left: Live Facebook Card Preview, Right: Stylish Caption & Quick Actions) -->
        <div class="p-5 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-5 items-start max-h-[82vh] overflow-y-auto">

            <!-- Left Column: Live Facebook Mobile/Desktop Feed Card Preview (7 cols) -->
            <div class="lg:col-span-7">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Live Facebook Post & Link Preview</span>
                    <span class="text-[10px] font-semibold text-emerald-600">100% Clickable Card</span>
                </div>

                <a :href="course.courseUrl || course.shortUrl"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="fb-link-preview-card group block w-full rounded-2xl overflow-hidden border border-[#dadde1] bg-white hover:bg-[#f7f8fa] transition-all duration-200 shadow-xs hover:shadow-md text-left no-underline">

                    <!-- Uppercase Mathematical Bold Serif Post Caption Header -->
                    <div class="px-4 py-3 bg-white border-b border-[#ebedf0] text-[13px] sm:text-sm font-serif font-bold text-[#050505] tracking-wide leading-snug"
                         x-text="course.postHeader"></div>

                    <!-- Course Photo / Thumbnail (Full Image Display Without Cropping) -->
                    <div class="relative w-full bg-slate-900/5 flex items-center justify-center overflow-hidden border-b border-[#dadde1]">
                        <img :src="course.image"
                             :alt="course.title"
                             class="w-full h-auto max-h-[520px] object-contain group-hover:scale-[1.01] transition-transform duration-300">
                    </div>

                    <!-- 2-Line Facebook Footer Bar (Domain + Bold Serif Title + Right-Side Glowing 'Enroll Now' Button) -->
                    <div class="px-4 py-3 bg-[#f2f3f5]/80 group-hover:bg-[#ebedf0]/90 transition-colors border-t border-[#dadde1] flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="text-xs sm:text-[13px] font-normal text-[#65676b] uppercase tracking-tight truncate" x-text="course.displayDomain"></div>
                            <h4 class="text-[15px] sm:text-base font-serif font-bold text-[#050505] group-hover:text-[#c30102] underline decoration-[#050505]/40 group-hover:decoration-[#c30102] transition-colors line-clamp-2 leading-snug mt-0.5 tracking-tight"
                                x-text="course.stylishTitle"></h4>
                        </div>
                        <span class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#c30102] to-[#e11d48] text-white font-extrabold text-xs sm:text-[13px] shrink-0 transition-all shadow-[0_0_18px_rgba(195,1,2,0.55)] group-hover:shadow-[0_0_24px_rgba(225,29,72,0.8)] group-hover:scale-105 inline-flex items-center gap-1.5 select-none">
                            <i class="fas fa-bolt text-amber-300 text-[11px] animate-pulse"></i>
                            <span>Enroll Now</span>
                        </span>
                    </div>
                </a>
            </div>

            <!-- Right Column: Stylish Caption Copy Box & 1-Click Share Actions (5 cols) -->
            <div class="lg:col-span-5 space-y-3.5">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Stylish Caption + Link</span>
                        <span class="text-[10px] font-bold text-[#1877F2]">Auto-Copied!</span>
                    </div>
                    <textarea readonly
                              rows="4"
                              x-model="course.socialCaption"
                              @click="$el.select(); copyText(course.socialCaption, 'full')"
                              class="w-full rounded-2xl border border-slate-200 bg-slate-50 p-3 text-xs sm:text-sm text-slate-900 font-serif font-bold leading-relaxed focus:outline-none select-all"></textarea>
                </div>

                <!-- Copy Action Buttons (Same as Admin Course Studio) -->
                <div class="grid grid-cols-1 gap-2">
                    <button type="button"
                            @click="copyText(course.socialCaption, 'full')"
                            class="w-full py-2.5 px-3.5 rounded-xl bg-[#051c45] hover:bg-[#082860] text-white text-xs font-extrabold transition-all flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                        <i class="fas fa-wand-magic-sparkles text-amber-300"></i>
                        <span x-text="copiedKey === 'full' ? '✓ Copied Full Stylish Post + Link!' : 'Copy Full Stylish Post + Link'"></span>
                    </button>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                @click="copyText(course.postHeader, 'header')"
                                class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#051c45] text-[11px] font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fas fa-font text-[#c30102]"></i>
                            <span x-text="copiedKey === 'header' ? '✓ Copied!' : 'Copy Bold Title'"></span>
                        </button>

                        <button type="button"
                                @click="copyText(course.shortUrl, 'link')"
                                class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#051c45] text-[11px] font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fas fa-link text-emerald-600"></i>
                            <span x-text="copiedKey === 'link' ? '✓ Copied!' : 'Copy Short Link'"></span>
                        </button>
                    </div>
                </div>

                <!-- Direct Social Share Launchers -->
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Post Directly To Social Feed</span>
                    <div class="grid grid-cols-2 gap-2">
                        <a :href="course.facebookShareUrl"
                           target="_blank"
                           rel="noopener noreferrer"
                           @click.prevent="copyText(course.postHeader, 'header'); window.open(course.facebookShareUrl, 'fbshare', 'width=640,height=560,menubar=no,toolbar=no');"
                           class="py-2.5 px-3 rounded-xl bg-[#1877F2] hover:bg-[#166fe5] text-white text-xs font-extrabold transition-all flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </a>

                        <a :href="course.whatsappShareUrl"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="py-2.5 px-3 rounded-xl bg-[#25D366] hover:bg-[#1da851] text-white text-xs font-extrabold transition-all flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                            <i class="fab fa-whatsapp text-sm"></i>
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

