@extends('layouts.admin')

@section('content')
<div class="mb-6 md:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#041536] text-white">
                Landing Page CMS
            </span>
            <span class="text-xs text-gray-500 font-medium">8 Dynamic Sections</span>
        </div>
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Landing Page Customizer</h2>
        <p class="text-gray-500 text-sm mt-1">Manage, rebrand, re-order content, and toggle visibility for every homepage section in real-time.</p>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="{{ url('/') }}" target="_blank" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 text-sm font-semibold shadow-sm inline-flex items-center gap-2 transition-all">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            <span>View Live Site</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 text-sm font-semibold flex items-center gap-3 shadow-sm">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@php
    $initialCustomPages = \App\Models\Setting::getCustomPages(false);
    $initialCustomSections = \App\Models\Setting::getCustomSections(false);
@endphp

<form action="{{ route('admin.frontend.store') }}" method="POST" enctype="multipart/form-data" 
      x-data="{ 
          activeTab: 'matrix',
          mode: '{{ $settings['landing_page_mode'] ?? 'default' }}',
          heroEnabled: {{ ($settings['landing_hero_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          clientsEnabled: {{ ($settings['landing_clients_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          categoriesEnabled: {{ ($settings['landing_categories_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          coursesEnabled: {{ ($settings['landing_courses_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          blogEnabled: {{ ($settings['landing_blog_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          counselingEnabled: {{ ($settings['landing_counseling_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          facultyEnabled: {{ ($settings['landing_faculty_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          featuresEnabled: {{ ($settings['landing_features_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          jobsEnabled: {{ ($settings['landing_jobs_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          pageJobsEnabled: {{ ($settings['page_jobs_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          pageBlogEnabled: {{ ($settings['page_blog_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          pageFeedEnabled: {{ ($settings['page_feed_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          pageInstructorsEnabled: {{ ($settings['page_instructors_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          pageAboutEnabled: {{ ($settings['page_about_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          pageContactEnabled: {{ ($settings['page_contact_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          pageVerifyEnabled: {{ ($settings['page_verify_certificate_enabled'] ?? '1') == '1' ? 'true' : 'false' }},
          customPages: @js($initialCustomPages),
          customSections: @js($initialCustomSections),
          addCustomPage() {
              this.customPages.push({
                  title: 'New Custom Page',
                  slug: 'custom-page-' + (this.customPages.length + 1),
                  subtitle: 'Executive Overview & Institutional Guidelines',
                  content: '<p>Enter your custom page content here.</p>',
                  meta_description: '',
                  is_published: true,
                  show_in_header: true,
                  show_in_footer: true
              });
          },
          removeCustomPage(idx) {
              this.customPages.splice(idx, 1);
          },
          addCustomSection() {
              this.customSections.push({
                  id: 'sec_' + Date.now(),
                  title: 'Executive Corporate Training & Advisory',
                  subtitle: 'Tailored capability development programs for commercial banks and trade enterprises.',
                  badge: 'Enterprise Solutions',
                  content: '<p>Partner with our distinguished faculty for customized in-house workshops and executive bootcamps.</p>',
                  cta_text: 'Request Corporate Proposal',
                  cta_url: '/contact',
                  theme: 'white',
                  is_visible: true
              });
          },
          removeCustomSection(idx) {
              this.customSections.splice(idx, 1);
          }
      }" 
      class="relative pb-24 md:pb-12">
    @csrf
    <input type="hidden" name="landing_cms_form_submitted" value="1">
    <input type="hidden" name="cms_custom_pages_json" :value="JSON.stringify(customPages)">
    <input type="hidden" name="landing_custom_sections_json" :value="JSON.stringify(customSections)">
    <input type="hidden" name="page_jobs_enabled" :value="pageJobsEnabled ? '1' : '0'">
    <input type="hidden" name="page_blog_enabled" :value="pageBlogEnabled ? '1' : '0'">
    <input type="hidden" name="page_feed_enabled" :value="pageFeedEnabled ? '1' : '0'">
    <input type="hidden" name="page_instructors_enabled" :value="pageInstructorsEnabled ? '1' : '0'">
    <input type="hidden" name="page_about_enabled" :value="pageAboutEnabled ? '1' : '0'">
    <input type="hidden" name="page_contact_enabled" :value="pageContactEnabled ? '1' : '0'">
    <input type="hidden" name="page_verify_certificate_enabled" :value="pageVerifyEnabled ? '1' : '0'">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Sidebar Navigation Tabs -->
        <div class="lg:col-span-3 space-y-1.5 sticky top-20 bg-white p-3 rounded-2xl border border-gray-200/80 shadow-sm">
            <div class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400">Production Command</div>

            <button type="button" @click="activeTab = 'matrix'" 
                    :class="activeTab === 'matrix' ? 'bg-[#051c45] text-white shadow-md' : 'text-slate-700 hover:bg-slate-100'"
                    class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-sliders text-sm text-emerald-400"></i>
                    <span>Section Control Matrix</span>
                </span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-emerald-500/20 text-emerald-300">All</span>
            </button>

            <button type="button" @click="activeTab = 'custom_sections'" 
                    :class="activeTab === 'custom_sections' ? 'bg-[#051c45] text-white shadow-md' : 'text-slate-700 hover:bg-slate-100'"
                    class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-plus-square text-sm text-[#c30102]"></i>
                    <span>Custom Sections Builder</span>
                </span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-slate-100 text-slate-700" x-text="customSections.length"></span>
            </button>

            <button type="button" @click="activeTab = 'pages'" 
                    :class="activeTab === 'pages' ? 'bg-[#051c45] text-white shadow-md' : 'text-slate-700 hover:bg-slate-100'"
                    class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-file-circle-plus text-sm text-blue-500"></i>
                    <span>Pages & Page Builder</span>
                </span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-800" x-text="customPages.length + ' Custom'"></span>
            </button>

            <div class="px-3 pt-3 pb-1 text-[10px] font-black uppercase tracking-widest text-slate-400 border-t border-slate-100">Homepage Sections (1–9)</div>

            <button type="button" @click="activeTab = 'hero'" 
                    :class="activeTab === 'hero' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-compass text-xs"></i>
                    <span>1. Executive Hero</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="heroEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'clients'" 
                    :class="activeTab === 'clients' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-handshake text-xs"></i>
                    <span>2. Valuable Clients</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="clientsEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'categories'" 
                    :class="activeTab === 'categories' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-layer-group text-xs"></i>
                    <span>3. Sticky Categories</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="categoriesEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'courses'" 
                    :class="activeTab === 'courses' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-graduation-cap text-xs"></i>
                    <span>4. Course Showcase</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="coursesEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'jobs'" 
                    :class="activeTab === 'jobs' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-briefcase text-xs"></i>
                    <span>5. Career & Jobs</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="jobsEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'counseling'" 
                    :class="activeTab === 'counseling' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-phone-volume text-xs"></i>
                    <span>6. Counseling Hotline</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="counselingEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'faculty'" 
                    :class="activeTab === 'faculty' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-user-tie text-xs"></i>
                    <span>7. Distinguished Faculty</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="facultyEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'features'" 
                    :class="activeTab === 'features' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-star text-xs"></i>
                    <span>8. Features (6 Cards)</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="featuresEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'blog'" 
                    :class="activeTab === 'blog' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-newspaper text-xs"></i>
                    <span>9. Knowledge Hub</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="blogEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <div class="px-3 pt-3 pb-1 text-[10px] font-black uppercase tracking-widest text-slate-400 border-t border-slate-100">Inner Pages Content</div>

            <button type="button" @click="activeTab = 'about'" 
                    :class="activeTab === 'about' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-university text-xs"></i>
                    <span>About Us Content</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="pageAboutEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'contact'" 
                    :class="activeTab === 'contact' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-map-marker-alt text-xs"></i>
                    <span>Contact Coordinates</span>
                </span>
                <span class="w-2 h-2 rounded-full" :class="pageContactEnabled ? 'bg-emerald-400' : 'bg-gray-300'"></span>
            </button>

            <button type="button" @click="activeTab = 'legal'" 
                    :class="activeTab === 'legal' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                <span class="flex items-center gap-2.5">
                    <i class="fas fa-balance-scale text-xs"></i>
                    <span>Legal & Policies</span>
                </span>
            </button>

            <div class="pt-2 border-t border-gray-100">
                <button type="button" @click="activeTab = 'mode'" 
                        :class="activeTab === 'mode' ? 'bg-[#041536] text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'"
                        class="w-full text-left px-3.5 py-2 rounded-xl text-xs font-bold flex items-center justify-between transition-all">
                    <span class="flex items-center gap-2.5">
                        <i class="fas fa-code text-xs"></i>
                        <span>Template Mode & Code</span>
                    </span>
                    <span class="text-[10px] uppercase font-bold text-gray-400" x-text="mode"></span>
                </button>
            </div>
            
            <div class="pt-4">
                <button type="submit" class="w-full py-3 px-4 bg-[#c30102] hover:bg-[#a50102] text-white font-bold rounded-xl shadow-lg shadow-red-900/20 active:scale-95 transition-all flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-save"></i>
                    <span>Save All Changes</span>
                </button>
            </div>
        </div>

        <!-- Tab Content Panels -->
        <div class="lg:col-span-9 space-y-6">

            <!-- ========================================== -->
            <!-- 0A. MASTER SECTION VISIBILITY MATRIX -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'matrix'" class="space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#051c45]/10 text-[#051c45] mb-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>1-Click Section Visibility Matrix</span>
                            </span>
                            <h3 class="text-lg sm:text-xl font-black text-[#051c45]">Homepage Sections Master Control</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Show or hide any landing page section in 1 click, or click "Edit Content" to customize its text, media, and CTAs.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="activeTab = 'custom_sections'" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#051c45] text-xs font-bold transition-colors">
                                + Add Custom Section
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white text-xs font-bold shadow-xs transition-colors">
                                Save Matrix
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
                        <!-- 1. Hero -->
                        <div class="p-4 rounded-xl border transition-all flex flex-col justify-between gap-3" :class="heroEnabled ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Section 01</span>
                                    <h4 class="text-sm font-extrabold text-[#051c45]">Executive Hero Banner</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Headline, tagline, trust pill, and video showcase.</p>
                                </div>
                                <input type="checkbox" x-model="heroEnabled" class="w-4 h-4 accent-emerald-600 rounded cursor-pointer mt-1">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase" :class="heroEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="heroEnabled ? 'Visible on Home' : 'Hidden'"></span>
                                <button type="button" @click="activeTab = 'hero'" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Edit Content →</button>
                            </div>
                        </div>

                        <!-- 2. Valuable Clients -->
                        <div class="p-4 rounded-xl border transition-all flex flex-col justify-between gap-3" :class="clientsEnabled ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Section 02</span>
                                    <h4 class="text-sm font-extrabold text-[#051c45]">Enterprise Sectors</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Industry sectors & corporate partner showcase.</p>
                                </div>
                                <input type="checkbox" x-model="clientsEnabled" class="w-4 h-4 accent-emerald-600 rounded cursor-pointer mt-1">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase" :class="clientsEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="clientsEnabled ? 'Visible on Home' : 'Hidden'"></span>
                                <button type="button" @click="activeTab = 'clients'" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Edit Content →</button>
                            </div>
                        </div>

                        <!-- 3. Sticky Categories -->
                        <div class="p-4 rounded-xl border transition-all flex flex-col justify-between gap-3" :class="categoriesEnabled ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Section 03</span>
                                    <h4 class="text-sm font-extrabold text-[#051c45]">Sticky Category Bar</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Quick horizontal category filter navigation.</p>
                                </div>
                                <input type="checkbox" x-model="categoriesEnabled" class="w-4 h-4 accent-emerald-600 rounded cursor-pointer mt-1">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase" :class="categoriesEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="categoriesEnabled ? 'Visible on Home' : 'Hidden'"></span>
                                <button type="button" @click="activeTab = 'categories'" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Edit Content →</button>
                            </div>
                        </div>

                        <!-- 4. Course Showcase -->
                        <div class="p-4 rounded-xl border transition-all flex flex-col justify-between gap-3" :class="coursesEnabled ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Section 04</span>
                                    <h4 class="text-sm font-extrabold text-[#051c45]">Course Catalog Grid</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Certificate & Day-Long Masterclass cards.</p>
                                </div>
                                <input type="checkbox" x-model="coursesEnabled" class="w-4 h-4 accent-emerald-600 rounded cursor-pointer mt-1">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase" :class="coursesEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="coursesEnabled ? 'Visible on Home' : 'Hidden'"></span>
                                <button type="button" @click="activeTab = 'courses'" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Edit Content →</button>
                            </div>
                        </div>

                        <!-- 5. Career & Jobs -->
                        <div class="p-4 rounded-xl border transition-all flex flex-col justify-between gap-3" :class="jobsEnabled ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Section 05</span>
                                    <h4 class="text-sm font-extrabold text-[#051c45]">Career & Placement Portal</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Featured corporate hiring & job listings.</p>
                                </div>
                                <input type="checkbox" x-model="jobsEnabled" class="w-4 h-4 accent-emerald-600 rounded cursor-pointer mt-1">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase" :class="jobsEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="jobsEnabled ? 'Visible on Home' : 'Hidden'"></span>
                                <button type="button" @click="activeTab = 'jobs'" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Edit Content →</button>
                            </div>
                        </div>

                        <!-- 6. Counseling Hotline -->
                        <div class="p-4 rounded-xl border transition-all flex flex-col justify-between gap-3" :class="counselingEnabled ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Section 06</span>
                                    <h4 class="text-sm font-extrabold text-[#051c45]">Executive Counseling CTA</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Direct phone & WhatsApp admissions strip.</p>
                                </div>
                                <input type="checkbox" x-model="counselingEnabled" class="w-4 h-4 accent-emerald-600 rounded cursor-pointer mt-1">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase" :class="counselingEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="counselingEnabled ? 'Visible on Home' : 'Hidden'"></span>
                                <button type="button" @click="activeTab = 'counseling'" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Edit Content →</button>
                            </div>
                        </div>

                        <!-- 7. Distinguished Faculty -->
                        <div class="p-4 rounded-xl border transition-all flex flex-col justify-between gap-3" :class="facultyEnabled ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Section 07</span>
                                    <h4 class="text-sm font-extrabold text-[#051c45]">Distinguished Faculty</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Industry practitioners & instructor profiles.</p>
                                </div>
                                <input type="checkbox" x-model="facultyEnabled" class="w-4 h-4 accent-emerald-600 rounded cursor-pointer mt-1">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase" :class="facultyEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="facultyEnabled ? 'Visible on Home' : 'Hidden'"></span>
                                <button type="button" @click="activeTab = 'faculty'" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Edit Content →</button>
                            </div>
                        </div>

                        <!-- 8. Features (6 Cards) -->
                        <div class="p-4 rounded-xl border transition-all flex flex-col justify-between gap-3" :class="featuresEnabled ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Section 08</span>
                                    <h4 class="text-sm font-extrabold text-[#051c45]">Institutional Advantages</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">6 value proposition cards on Imperial Navy.</p>
                                </div>
                                <input type="checkbox" x-model="featuresEnabled" class="w-4 h-4 accent-emerald-600 rounded cursor-pointer mt-1">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase" :class="featuresEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="featuresEnabled ? 'Visible on Home' : 'Hidden'"></span>
                                <button type="button" @click="activeTab = 'features'" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Edit Content →</button>
                            </div>
                        </div>

                        <!-- 9. Knowledge Hub -->
                        <div class="p-4 rounded-xl border transition-all flex flex-col justify-between gap-3" :class="blogEnabled ? 'bg-white border-slate-200 shadow-2xs' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Section 09</span>
                                    <h4 class="text-sm font-extrabold text-[#051c45]">Knowledge Hub & Blogs</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Latest trade & banking articles grid.</p>
                                </div>
                                <input type="checkbox" x-model="blogEnabled" class="w-4 h-4 accent-emerald-600 rounded cursor-pointer mt-1">
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-black uppercase" :class="blogEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="blogEnabled ? 'Visible on Home' : 'Hidden'"></span>
                                <button type="button" @click="activeTab = 'blog'" class="text-xs font-bold text-[#051c45] hover:text-[#c30102]">Edit Content →</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 0B. CUSTOM HOMEPAGE SECTIONS BUILDER -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'custom_sections'" class="space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-red-50 text-[#c30102]">Dynamic Section Builder</span>
                            <h3 class="text-xl font-black text-[#051c45] mt-1">Custom Homepage Sections</h3>
                            <p class="text-xs text-slate-500">Create unlimited custom sections on the landing page and toggle their visibility anytime without writing code.</p>
                        </div>
                        <button type="button" @click="addCustomSection()" class="px-4 py-2.5 rounded-xl bg-[#051c45] hover:bg-[#0a2540] text-white text-xs font-bold inline-flex items-center gap-2 shadow-xs transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Add New Section</span>
                        </button>
                    </div>

                    <template x-if="customSections.length === 0">
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <i class="fas fa-layer-group text-2xl text-slate-400 mb-2"></i>
                            <p class="text-sm font-bold text-slate-700">No custom homepage sections created yet.</p>
                            <p class="text-xs text-slate-500 mt-1 mb-4">Click "Add New Section" to add corporate training banners, scholarship announcements, or custom blocks.</p>
                            <button type="button" @click="addCustomSection()" class="px-4 py-2 rounded-xl bg-[#c30102] text-white text-xs font-bold">+ Create First Custom Section</button>
                        </div>
                    </template>

                    <div class="space-y-4">
                        <template x-for="(sec, idx) in customSections" :key="idx">
                            <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-4">
                                <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2.5 py-1 rounded-lg bg-[#051c45] text-white text-xs font-black" x-text="'Custom Section #' + (idx + 1)"></span>
                                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold">
                                            <input type="checkbox" x-model="sec.is_visible" class="w-4 h-4 accent-emerald-600 rounded">
                                            <span :class="sec.is_visible ? 'text-emerald-700' : 'text-slate-400'" x-text="sec.is_visible ? 'Visible on Homepage' : 'Hidden'"></span>
                                        </label>
                                    </div>
                                    <button type="button" @click="removeCustomSection(idx)" class="text-xs font-bold text-rose-600 hover:text-rose-800 px-2.5 py-1 rounded-lg hover:bg-rose-50">
                                        <i class="fas fa-trash-can mr-1"></i>Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Section Heading</label>
                                        <input type="text" x-model="sec.title" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-bold text-[#051c45]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Visual Theme</label>
                                        <select x-model="sec.theme" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-800">
                                            <option value="white">Crisp Alabaster White</option>
                                            <option value="slate">Soft Slate Canvas</option>
                                            <option value="navy">Imperial Navy Executive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Top Pill Badge</label>
                                        <input type="text" x-model="sec.badge" placeholder="e.g. Corporate Program" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Subtitle</label>
                                        <input type="text" x-model="sec.subtitle" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Section Body Content (HTML or Text)</label>
                                    <textarea x-model="sec.content" rows="3" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-mono"></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">CTA Button Label (Optional)</label>
                                        <input type="text" x-model="sec.cta_text" placeholder="e.g. Request Proposal" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">CTA Target URL</label>
                                        <input type="text" x-model="sec.cta_url" placeholder="/contact" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-mono">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 1. EXECUTIVE HERO SECTION -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'hero'" class="space-y-6">
                
                <!-- Visibility Toggle Card -->
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Hero Section Visibility</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Toggle whether the top flagship hero canvas is rendered on the home page.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="landing_hero_enabled" :value="heroEnabled ? '1' : '0'">
                        <input type="checkbox" x-model="heroEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-xs font-extrabold uppercase" :class="heroEnabled ? 'text-emerald-700' : 'text-gray-400'" x-text="heroEnabled ? 'Active' : 'Hidden'"></span>
                    </label>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="font-black text-gray-900 text-lg">Hero Copy & Brand Messaging</h4>
                        <p class="text-xs text-gray-500">Fine-tune the headline, high-impact pill tagline, and value proposition.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Hero Title (H1)</label>
                            <input type="text" name="landing_hero_title" value="{{ $settings['landing_hero_title'] ?? 'CAB Academy' }}" 
                                   placeholder="CAB Academy" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tagline (Crimson Pill)</label>
                            <input type="text" name="landing_hero_tagline" value="{{ $settings['landing_hero_tagline'] ?? 'Learn, Grow, Lead.' }}" 
                                   placeholder="Learn, Grow, Lead." 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Hero Subtitle / Description</label>
                        <textarea name="landing_hero_subtitle" rows="2" 
                                  placeholder="Online Professional Certification Masterclasses & Corporate Programs." 
                                  class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none">{{ $settings['landing_hero_subtitle'] ?? 'Online Professional Certification Masterclasses & Corporate Programs.' }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/80 space-y-3">
                            <h5 class="text-xs font-black uppercase text-gray-800">Primary CTA Button (Crimson)</h5>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Button Text</label>
                                <input type="text" name="landing_hero_cta1_text" value="{{ $settings['landing_hero_cta1_text'] ?? ($settings['landing_cta_text'] ?? 'Explore Courses') }}" 
                                       placeholder="Explore Courses" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Button URL / Anchor</label>
                                <input type="text" name="landing_hero_cta1_url" value="{{ $settings['landing_hero_cta1_url'] ?? '#home-category' }}" 
                                       placeholder="#home-category" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/80 space-y-3">
                            <h5 class="text-xs font-black uppercase text-gray-800">Secondary CTA Button (Glass)</h5>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Button Text</label>
                                <input type="text" name="landing_hero_cta2_text" value="{{ $settings['landing_hero_cta2_text'] ?? 'Export – Import' }}" 
                                       placeholder="Export – Import" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Button URL / Route</label>
                                <input type="text" name="landing_hero_cta2_url" value="{{ $settings['landing_hero_cta2_url'] ?? '/category/export-import' }}" 
                                       placeholder="/category/export-import" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Social Proof / Alumni Counter Line</label>
                        <input type="text" name="landing_hero_alumni_text" value="{{ $settings['landing_hero_alumni_text'] ?? '10,000+ Corporate Alumni' }}" 
                               placeholder="10,000+ Corporate Alumni" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                    </div>

                    <div class="pt-2 border-t border-gray-100 space-y-4">
                        <h4 class="font-black text-gray-900 text-base">Right Visual Showcase Bezel</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Poster Image URL</label>
                                <input type="text" name="landing_hero_media_image" value="{{ $settings['landing_hero_media_image'] ?? 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1000&q=80' }}" 
                                       placeholder="https://..." 
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Or Upload New Poster Image</label>
                                <input type="file" name="landing_hero_media_image_file" accept="image/*" 
                                       class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#041536] file:text-white hover:file:opacity-90">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Play Button Click Action / Link</label>
                            <input type="text" name="landing_hero_media_link" value="{{ $settings['landing_hero_media_link'] ?? '/courses' }}" 
                                   placeholder="/courses" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 2. VALUABLE CLIENTS -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'clients'" class="space-y-6">
                
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Clients Section Visibility</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Toggle the corporate client logo ribbon below the hero.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="landing_clients_enabled" :value="clientsEnabled ? '1' : '0'">
                        <input type="checkbox" x-model="clientsEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-xs font-extrabold uppercase" :class="clientsEnabled ? 'text-emerald-700' : 'text-gray-400'" x-text="clientsEnabled ? 'Active' : 'Hidden'"></span>
                    </label>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Section Heading</label>
                        <input type="text" name="landing_clients_title" value="{{ $settings['landing_clients_title'] ?? 'Our Valuable Clients' }}" 
                               placeholder="Our Valuable Clients" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Client List (One per line: Name | Logo URL)</label>
                            <span class="text-xs text-gray-400 font-medium">Leave blank for authentic defaults</span>
                        </div>
                        <textarea name="landing_clients_data" rows="9" 
                                  placeholder="International Trade & L/C&#10;Supply Chain Management&#10;Customs & Freight Clearance&#10;Corporate Tax & VAT&#10;Commercial Banking & Finance&#10;Industrial HR & Labor Laws&#10;Corporate Law & Compliance&#10;Project Planning & BOQ" 
                                  class="w-full px-4 py-3 rounded-xl border border-gray-300 text-xs font-mono focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">{{ $settings['landing_clients_data'] ?? '' }}</textarea>
                        <p class="text-[11px] text-gray-500 mt-1">Format: <code>Sector / Partner Name | Logo URL</code> (if logo URL is omitted, the clean name badge is rendered automatically).</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 3. STICKY CATEGORIES -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'categories'" class="space-y-6">
                
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Sticky Smart Categories Bar Visibility</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Toggle the compact, icon-driven sticky navigation bar across the course section.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="landing_categories_enabled" :value="categoriesEnabled ? '1' : '0'">
                        <input type="checkbox" x-model="categoriesEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-xs font-extrabold uppercase" :class="categoriesEnabled ? 'text-emerald-700' : 'text-gray-400'" x-text="categoriesEnabled ? 'Active' : 'Hidden'"></span>
                    </label>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="md:col-span-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Section Label</label>
                            <input type="text" name="landing_categories_title" value="{{ $settings['landing_categories_title'] ?? 'Course Categories' }}" 
                                   placeholder="Course Categories" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">"View All" Button Text</label>
                            <input type="text" name="landing_categories_all_text" value="{{ $settings['landing_categories_all_text'] ?? 'All' }}" 
                                   placeholder="All" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">"View All" URL</label>
                            <input type="text" name="landing_categories_all_url" value="{{ $settings['landing_categories_all_url'] ?? '/courses' }}" 
                                   placeholder="/courses" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Category Pills (One per line: Name | slug | fontawesome-icon)</label>
                            <span class="text-xs text-gray-400 font-medium">Leave blank for authentic 12 defaults</span>
                        </div>
                        <textarea name="landing_categories_data" rows="12" 
                                  placeholder="Soft Skills | soft-skills | fa-comments&#10;Supply Chain Management | supply-chain-management | fa-truck-fast&#10;Value Added Tax | value-added-tax | fa-file-invoice-dollar&#10;Export – Import | export-import | fa-ship&#10;Accounting | accounting | fa-calculator&#10;Human Resource | human-resource | fa-people-group&#10;Civil Construction Project | civil-construction-project | fa-helmet-safety&#10;Fire Safety | fire-safety | fa-fire-extinguisher&#10;Law & Compliance | law | fa-scale-balanced&#10;Leadership | leadership | fa-award&#10;Production & Ops | production | fa-industry&#10;Project Management | project-management | fa-diagram-project" 
                                  class="w-full px-4 py-3 rounded-xl border border-gray-300 text-xs font-mono focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">{{ $settings['landing_categories_data'] ?? '' }}</textarea>
                        <p class="text-[11px] text-gray-500 mt-1">Format: <code>Category Name | slug | icon</code>. Example: <code>Export – Import | export-import | fa-ship</code>.</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 4. DUAL COURSE SHOWCASE -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'courses'" class="space-y-6">
                
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Course Showcase Visibility</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Toggle the Certificate Course & Day Long Course dual-column section.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="landing_courses_enabled" :value="coursesEnabled ? '1' : '0'">
                        <input type="checkbox" x-model="coursesEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-xs font-extrabold uppercase" :class="coursesEnabled ? 'text-emerald-700' : 'text-gray-400'" x-text="coursesEnabled ? 'Active' : 'Hidden'"></span>
                    </label>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-6">
                    <!-- Central Bracket Header -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <h4 class="font-black text-gray-900 text-sm uppercase">Central Connecting Bracket Header</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Bracket Button Text</label>
                                <input type="text" name="landing_courses_bracket_text" value="{{ $settings['landing_courses_bracket_text'] ?? 'View All Courses' }}" 
                                       placeholder="View All Courses" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Bracket Button URL</label>
                                <input type="text" name="landing_courses_bracket_url" value="{{ $settings['landing_courses_bracket_url'] ?? '/courses' }}" 
                                       placeholder="/courses" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>
                    </div>

                    <!-- Left Track: Certificate Course -->
                    <div class="p-5 bg-emerald-50/50 rounded-xl border border-emerald-200/80 space-y-4">
                        <div class="flex items-center justify-between border-b border-emerald-200/60 pb-2">
                            <h4 class="font-black text-emerald-950 text-base flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span>Left Container: Certificate Course Track</span>
                            </h4>
                            <span class="text-xs text-emerald-800 font-bold bg-emerald-100 px-2.5 py-0.5 rounded-full">Light Green Container (.bg-[#f0f8ec])</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Track Badge</label>
                                <input type="text" name="landing_cert_badge" value="{{ $settings['landing_cert_badge'] ?? 'Executive Master Track' }}" 
                                       placeholder="Executive Master Track" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Container Heading</label>
                                <input type="text" name="landing_cert_title" value="{{ $settings['landing_cert_title'] ?? 'Certificate Course' }}" 
                                       placeholder="Certificate Course" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Courses To Display</label>
                                <input type="number" min="2" max="12" name="landing_cert_count" value="{{ $settings['landing_cert_count'] ?? '4' }}" 
                                       placeholder="4" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Bottom Action Button Text</label>
                                <input type="text" name="landing_cert_btn_text" value="{{ $settings['landing_cert_btn_text'] ?? 'View All Certificate Courses' }}" 
                                       placeholder="View All Certificate Courses" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Bottom Action Button URL</label>
                                <input type="text" name="landing_cert_btn_url" value="{{ $settings['landing_cert_btn_url'] ?? '/courses/certificate' }}" 
                                       placeholder="/courses/certificate" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>
                    </div>

                    <!-- Right Track: Day Long Course -->
                    <div class="p-5 bg-red-50/40 rounded-xl border border-red-200/80 space-y-4">
                        <div class="flex items-center justify-between border-b border-red-200/60 pb-2">
                            <h4 class="font-black text-red-950 text-base flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#c30102]"></span>
                                <span>Right Container: Day Long Course Track</span>
                            </h4>
                            <span class="text-xs text-red-800 font-bold bg-red-100 px-2.5 py-0.5 rounded-full">Light Peach Container (.bg-[#fef4f0])</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Track Badge</label>
                                <input type="text" name="landing_daylong_badge" value="{{ $settings['landing_daylong_badge'] ?? '1-Day Accelerator' }}" 
                                       placeholder="1-Day Accelerator" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Container Heading</label>
                                <input type="text" name="landing_daylong_title" value="{{ $settings['landing_daylong_title'] ?? 'Day Long Course' }}" 
                                       placeholder="Day Long Course" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Courses To Display</label>
                                <input type="number" min="2" max="12" name="landing_daylong_count" value="{{ $settings['landing_daylong_count'] ?? '4' }}" 
                                       placeholder="4" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Bottom Action Button Text</label>
                                <input type="text" name="landing_daylong_btn_text" value="{{ $settings['landing_daylong_btn_text'] ?? 'View All Day Long Courses' }}" 
                                       placeholder="View All Day Long Courses" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Bottom Action Button URL</label>
                                <input type="text" name="landing_daylong_btn_url" value="{{ $settings['landing_daylong_btn_url'] ?? '/courses/day-long' }}" 
                                       placeholder="/courses/day-long" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 5. KNOWLEDGE HUB / BLOG -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'blog'" class="space-y-6">
                
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Knowledge Hub / Blog Visibility</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Toggle the insights, publications, and thought leadership articles section.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="landing_blog_enabled" :value="blogEnabled ? '1' : '0'">
                        <input type="checkbox" x-model="blogEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-xs font-extrabold uppercase" :class="blogEnabled ? 'text-emerald-700' : 'text-gray-400'" x-text="blogEnabled ? 'Active' : 'Hidden'"></span>
                    </label>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Section Heading</label>
                            <input type="text" name="landing_blog_title" value="{{ $settings['landing_blog_title'] ?? 'Knowledge Hub & Publications' }}" 
                                   placeholder="Knowledge Hub & Publications" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Articles To Display</label>
                            <input type="number" min="1" max="12" name="landing_blog_count" value="{{ $settings['landing_blog_count'] ?? '3' }}" 
                                   placeholder="3" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Subtitle / Faculty Insights Blurb</label>
                        <textarea name="landing_blog_subtitle" rows="2" 
                                  placeholder="Thought leadership, customs directives, tax reforms, and supply chain insights by CAB Academy Distinguished Faculty." 
                                  class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none">{{ $settings['landing_blog_subtitle'] ?? 'Thought leadership, customs directives, tax reforms, and supply chain insights by CAB Academy Distinguished Faculty.' }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">"View All" Button Text</label>
                            <input type="text" name="landing_blog_btn_text" value="{{ $settings['landing_blog_btn_text'] ?? 'View All Publications' }}" 
                                   placeholder="View All Publications" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">"View All" Button URL</label>
                            <input type="text" name="landing_blog_btn_url" value="{{ $settings['landing_blog_btn_url'] ?? '/blog' }}" 
                                   placeholder="/blog" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 6. COUNSELING & ADMISSIONS HOTLINE -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'counseling'" class="space-y-6">
                
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Admissions & Counseling Section Visibility</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Toggle the high-conversion emerald admissions hotline and instant WhatsApp counseling banner.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="landing_counseling_enabled" :value="counselingEnabled ? '1' : '0'">
                        <input type="checkbox" x-model="counselingEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-xs font-extrabold uppercase" :class="counselingEnabled ? 'text-emerald-700' : 'text-gray-400'" x-text="counselingEnabled ? 'Active' : 'Hidden'"></span>
                    </label>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Section Title</label>
                        <input type="text" name="landing_counseling_title" value="{{ $settings['landing_counseling_title'] ?? 'Need To Learn More ?' }}" 
                               placeholder="Need To Learn More ?" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Subtitle / Guidance Text</label>
                        <textarea name="landing_counseling_subtitle" rows="2" 
                                  placeholder="Connect with our academic counselors for corporate training customized for your organization, batch reservations, or curriculum guidance." 
                                  class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none">{{ $settings['landing_counseling_subtitle'] ?? 'Connect with our academic counselors for corporate training customized for your organization, batch reservations, or curriculum guidance.' }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2 border-t border-gray-100">
                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/80 space-y-3">
                            <h5 class="text-xs font-black uppercase text-gray-800 flex items-center gap-1.5">
                                <i class="fas fa-phone-alt text-[#c30102]"></i>
                                <span>Phone Hotline</span>
                            </h5>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Display Label</label>
                                <input type="text" name="landing_counseling_phone_label" value="{{ $settings['landing_counseling_phone_label'] ?? 'Call: 01917-054664' }}" 
                                       placeholder="Call: 01917-054664" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Dialable Number (tel:)</label>
                                <input type="text" name="landing_counseling_phone_tel" value="{{ $settings['landing_counseling_phone_tel'] ?? '01917054664' }}" 
                                       placeholder="01917054664" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/80 space-y-3">
                            <h5 class="text-xs font-black uppercase text-gray-800 flex items-center gap-1.5">
                                <i class="fab fa-whatsapp text-emerald-600"></i>
                                <span>Instant WhatsApp</span>
                            </h5>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Button Text</label>
                                <input type="text" name="landing_counseling_wa_label" value="{{ $settings['landing_counseling_wa_label'] ?? 'Instant WhatsApp' }}" 
                                       placeholder="Instant WhatsApp" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">WhatsApp International Number</label>
                                <input type="text" name="landing_counseling_wa_number" value="{{ $settings['landing_counseling_wa_number'] ?? '8801917054664' }}" 
                                       placeholder="8801917054664" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/80 space-y-3">
                        <h5 class="text-xs font-black uppercase text-gray-800">Third Button (Category / Track Link)</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Button Text</label>
                                <input type="text" name="landing_counseling_btn3_text" value="{{ $settings['landing_counseling_btn3_text'] ?? 'Explore Export – Import' }}" 
                                       placeholder="Explore Export – Import" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Button URL</label>
                                <input type="text" name="landing_counseling_btn3_url" value="{{ $settings['landing_counseling_btn3_url'] ?? '/category/export-import' }}" 
                                       placeholder="/category/export-import" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 7. DISTINGUISHED FACULTY & FELLOWS -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'faculty'" class="space-y-6">
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Faculty Showcase Visibility</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Showcase verified executive faculty members dynamically on the home/landing page.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="landing_faculty_enabled" :value="facultyEnabled ? '1' : '0'">
                        <input type="checkbox" x-model="facultyEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="flex items-center justify-between border-b pb-4">
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Section Headings</h4>
                            <p class="text-xs text-gray-500">Configure title displayed on the public home page.</p>
                        </div>
                        <a href="{{ route('admin.instructors.index') }}" class="px-3.5 py-1.5 bg-[#041536] hover:bg-[#c30102] text-white text-xs font-bold rounded-xl transition-all shadow-xs inline-flex items-center gap-1.5">
                            <i class="fas fa-user-plus text-[10px]"></i>
                            <span>Manage Faculty & Photos →</span>
                        </a>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Section Title</label>
                        <input type="text" name="landing_faculty_title" value="{{ $settings['landing_faculty_title'] ?? 'Distinguished Faculty & Industry Fellows' }}" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-900 focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 flex items-start gap-3">
                        <i class="fas fa-info-circle text-[#041536] mt-0.5 text-sm"></i>
                        <div>
                            <span class="font-bold text-gray-900">Dynamic Database Sync:</span>
                            The landing page automatically showcases all active instructors with their photo, title, and expertise directly from the <a href="{{ route('admin.instructors.index') }}" class="text-[#c30102] font-bold underline hover:text-black">Instructors Directory</a>. Any update to a profile or picture immediately reflects live on the homepage!
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 8. OUR FEATURES (6 CARDS) -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'features'" class="space-y-6">
                
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Features Section Visibility</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Toggle the 6 institutional capability cards and distinguished mentorship section.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="landing_features_enabled" :value="featuresEnabled ? '1' : '0'">
                        <input type="checkbox" x-model="featuresEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-xs font-extrabold uppercase" :class="featuresEnabled ? 'text-emerald-700' : 'text-gray-400'" x-text="featuresEnabled ? 'Active' : 'Hidden'"></span>
                    </label>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Section Title</label>
                        <input type="text" name="landing_features_title" value="{{ $settings['landing_features_title'] ?? 'Our Features' }}" 
                               placeholder="Our Features" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                    </div>

                    @php
                        $featureDefaults = [
                            1 => ['icon' => 'fa-graduation-cap', 'title' => 'Industry Level Curriculum', 'desc' => 'Designed alongside top corporate practitioners and customs specialists to fulfill active corporate requirements.'],
                            2 => ['icon' => 'fa-chalkboard-teacher', 'title' => 'Distinguished Faculty & Fellows', 'desc' => 'Direct executive mentorship guided by high-profile corporate leaders, senior chartered accountants, NBR tax advocates, and logistics directors.'],
                            3 => ['icon' => 'fa-laptop-house', 'title' => '100% Live Interactive Online', 'desc' => 'Live Zoom masterclasses with real-time Q&A, session recordings, and cloud course materials accessible from anywhere.'],
                            4 => ['icon' => 'fa-certificate', 'title' => 'Recognized Certifications', 'desc' => 'Credential verified certifications recognized by leading industrial groups across Bangladesh.'],
                            5 => ['icon' => 'fa-headset', 'title' => 'Support Services', 'desc' => 'Post-training support, consultation, documentation guidance, and active peer professional networking.'],
                            6 => ['icon' => 'fa-layer-group', 'title' => 'Wide Range of Courses', 'desc' => 'Over 14 professional sectors covering Import-Export, SCM, VAT, HR, Civil Engineering, and Corporate Tax.'],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @for($i = 1; $i <= 6; $i++)
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200/80 space-y-3">
                                <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                                    <h5 class="text-xs font-black uppercase text-gray-800">Feature Card {{ $i }}</h5>
                                    <span class="text-[10px] text-gray-500 font-mono">landing_feature_{{ $i }}</span>
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1">FontAwesome Icon</label>
                                        <input type="text" name="landing_feature_{{ $i }}_icon" 
                                               value="{{ $settings["landing_feature_{$i}_icon"] ?? $featureDefaults[$i]['icon'] }}" 
                                               placeholder="{{ $featureDefaults[$i]['icon'] }}" 
                                               class="w-full px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-mono">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-gray-700 mb-1">Card Title</label>
                                        <input type="text" name="landing_feature_{{ $i }}_title" 
                                               value="{{ $settings["landing_feature_{$i}_title"] ?? $featureDefaults[$i]['title'] }}" 
                                               placeholder="{{ $featureDefaults[$i]['title'] }}" 
                                               class="w-full px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-semibold">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 mb-1">Description</label>
                                    <textarea name="landing_feature_{{ $i }}_desc" rows="2" 
                                              placeholder="{{ $featureDefaults[$i]['desc'] }}" 
                                              class="w-full px-3 py-1.5 rounded-lg border border-gray-300 text-xs resize-none">{{ $settings["landing_feature_{$i}_desc"] ?? $featureDefaults[$i]['desc'] }}</textarea>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 8. CAREER & JOBS -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'jobs'" class="space-y-6">
                
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Career Opportunities Section Visibility</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Toggle the exclusive job and placement openings section.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="landing_jobs_enabled" :value="jobsEnabled ? '1' : '0'">
                        <input type="checkbox" x-model="jobsEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-xs font-extrabold uppercase" :class="jobsEnabled ? 'text-emerald-700' : 'text-gray-400'" x-text="jobsEnabled ? 'Active' : 'Hidden'"></span>
                    </label>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Section Title</label>
                        <input type="text" name="landing_jobs_title" value="{{ $settings['landing_jobs_title'] ?? 'Career & Placement Opportunities' }}" 
                               placeholder="Career & Placement Opportunities" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Subtitle / Explainer</label>
                        <textarea name="landing_jobs_subtitle" rows="2" 
                                  placeholder="Explore openings posted exclusively for CAB Academy certified professionals" 
                                  class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none">{{ $settings['landing_jobs_subtitle'] ?? 'Explore openings posted exclusively for CAB Academy certified professionals' }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Action Button Text</label>
                            <input type="text" name="landing_jobs_btn_text" value="{{ $settings['landing_jobs_btn_text'] ?? 'Browse All Openings' }}" 
                                   placeholder="Browse All Openings" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Action Button URL</label>
                            <input type="text" name="landing_jobs_btn_url" value="{{ $settings['landing_jobs_btn_url'] ?? '/jobs' }}" 
                                   placeholder="/jobs" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 9. ABOUT US PAGE CMS -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'about'" class="space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-100 text-[#051c45]">Inner Page</span>
                            <span class="text-xs text-gray-500 font-semibold">/about</span>
                        </div>
                        <h3 class="text-lg font-black text-gray-900">About Us Page Customizer</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Control the hero heading, statistical performance counters, mission, vision, and core institutional philosophy.</p>
                    </div>

                    <!-- Hero Section -->
                    <div class="pt-3 border-t border-gray-100 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">1. Hero Section</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Hero Pill / Badge</label>
                            <input type="text" name="about_hero_badge" value="{{ $settings['about_hero_badge'] ?? 'Commercial Academy Bangladesh' }}" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Hero Headline Title</label>
                            <input type="text" name="about_hero_title" value="{{ $settings['about_hero_title'] ?? 'South Asia\'s Premier Institute for Commercial Trade & Industry Mastery' }}" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Hero Narrative Description</label>
                            <textarea name="about_hero_desc" rows="3" 
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none">{{ $settings['about_hero_desc'] ?? 'Commercial Academy Bangladesh (CAB Academy) stands as the gold standard for executive trade education, supply chain logistics, customs clearance, and corporate compliance.' }}</textarea>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="pt-4 border-t border-gray-100 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">2. Statistical Metric Counters</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Metric 1 Counter</label>
                                <input type="text" name="about_stat_graduates" value="{{ $settings['about_stat_graduates'] ?? '5,000+' }}" 
                                       class="w-full px-3 py-2 bg-white rounded-lg border border-gray-300 text-sm mb-2">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Metric 1 Label</label>
                                <input type="text" name="about_stat_graduates_label" value="{{ $settings['about_stat_graduates_label'] ?? 'Corporate Alumni' }}" 
                                       class="w-full px-3 py-2 bg-white rounded-lg border border-gray-300 text-xs">
                            </div>

                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Metric 2 Counter</label>
                                <input type="text" name="about_stat_curriculum" value="{{ $settings['about_stat_curriculum'] ?? '98%' }}" 
                                       class="w-full px-3 py-2 bg-white rounded-lg border border-gray-300 text-sm mb-2">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Metric 2 Label</label>
                                <input type="text" name="about_stat_curriculum_label" value="{{ $settings['about_stat_curriculum_label'] ?? 'Practical Curriculum' }}" 
                                       class="w-full px-3 py-2 bg-white rounded-lg border border-gray-300 text-xs">
                            </div>

                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Metric 3 Counter</label>
                                <input type="text" name="about_stat_faculty" value="{{ $settings['about_stat_faculty'] ?? '25+' }}" 
                                       class="w-full px-3 py-2 bg-white rounded-lg border border-gray-300 text-sm mb-2">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Metric 3 Label</label>
                                <input type="text" name="about_stat_faculty_label" value="{{ $settings['about_stat_faculty_label'] ?? 'Distinguished Faculty' }}" 
                                       class="w-full px-3 py-2 bg-white rounded-lg border border-gray-300 text-xs">
                            </div>

                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Metric 4 Counter</label>
                                <input type="text" name="about_stat_partners" value="{{ $settings['about_stat_partners'] ?? '120+' }}" 
                                       class="w-full px-3 py-2 bg-white rounded-lg border border-gray-300 text-sm mb-2">
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Metric 4 Label</label>
                                <input type="text" name="about_stat_partners_label" value="{{ $settings['about_stat_partners_label'] ?? 'Corporate Partners' }}" 
                                       class="w-full px-3 py-2 bg-white rounded-lg border border-gray-300 text-xs">
                            </div>
                        </div>
                    </div>

                    <!-- Mission & Vision -->
                    <div class="pt-4 border-t border-gray-100 space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">3. Mission, Vision & Core Values</h4>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Mission Statement Title & Text</label>
                            <input type="text" name="about_mission_title" value="{{ $settings['about_mission_title'] ?? 'Our Mission' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-gray-300 text-sm mb-2 font-bold">
                            <textarea name="about_mission_text" rows="3" 
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none">{{ $settings['about_mission_text'] ?? 'To engineer real-world competency and career breakthroughs for professionals through industry-vetted coursework, simulated trade laboratories, and personalized mentoring by C-suite practitioners.' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Vision Statement Title & Text</label>
                            <input type="text" name="about_vision_title" value="{{ $settings['about_vision_title'] ?? 'Our Vision' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-gray-300 text-sm mb-2 font-bold">
                            <textarea name="about_vision_text" rows="3" 
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none">{{ $settings['about_vision_text'] ?? 'To be South Asia\'s preeminent institute for commercial trade, customs navigation, and supply chain excellence, recognized worldwide for ethical leadership and operational mastery.' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Core Philosophy Title & Text</label>
                            <input type="text" name="about_philosophy_title" value="{{ $settings['about_philosophy_title'] ?? 'Core Philosophy' }}" 
                                   class="w-full px-4 py-2 rounded-xl border border-gray-300 text-sm mb-2 font-bold">
                            <textarea name="about_philosophy_text" rows="3" 
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536] resize-none">{{ $settings['about_philosophy_text'] ?? 'Zero Fluff, 100% Applicability. Every case study, L/C draft, bill of lading, and VAT audit calculation directly reflects the current regulatory landscape of the National Board of Revenue (NBR) and Bangladesh Bank.' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 10. CONTACT & CAMPUS COORDINATES -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'contact'" class="space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800">Global Coordinates</span>
                            <span class="text-xs text-gray-500 font-semibold">Universal Header, Footer & Contact Page</span>
                        </div>
                        <h3 class="text-lg font-black text-gray-900">Campus Contact & Communication Channels</h3>
                        <p class="text-xs text-gray-500 mt-0.5">These official coordinates automatically synchronize across the topbar, navigation drawer, contact page, and website footer.</p>
                    </div>

                    <div class="space-y-4 pt-3 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Campus Name / Facility Title</label>
                            <input type="text" name="contact_campus_name" value="{{ $settings['contact_campus_name'] ?? 'CAB Academy Corporate Campus' }}" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Physical Campus Address</label>
                            <input type="text" name="contact_address" value="{{ $settings['contact_address'] ?? 'Mirpur-Pallabi, Dhaka, Bangladesh, 1216' }}" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Executive Hotline (Displayed)</label>
                                <input type="text" name="contact_hotline" value="{{ $settings['contact_hotline'] ?? '01917-054664' }}" 
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Official Inquiries Email</label>
                                <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'commercialacademy868@gmail.com' }}" 
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">WhatsApp Number (e.g. 8801917054664)</label>
                                <input type="text" name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] ?? '8801917054664' }}" 
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Office & Support Hours</label>
                                <input type="text" name="contact_hours" value="{{ $settings['contact_hours'] ?? '9:00 AM – 8:00 PM, Saturday to Thursday' }}" 
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 11. LEGAL POLICIES CMS -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'legal'" class="space-y-6">
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-red-100 text-[#c30102]">Compliance Desk</span>
                            <span class="text-xs text-gray-500 font-semibold">Privacy, Terms & Refund Policies</span>
                        </div>
                        <h3 class="text-lg font-black text-gray-900">Legal Policy Overrides</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Leave any field blank to preserve CAB Academy's default pre-vetted corporate legal policy.</p>
                    </div>

                    <div class="space-y-5 pt-3 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Privacy Policy Content (HTML / Text)</label>
                            <textarea name="policy_privacy_content" rows="6" 
                                      placeholder="Leave blank to use default corporate privacy policy..." 
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#041536] focus:ring-1 focus:ring-[#041536] font-mono">{{ $settings['policy_privacy_content'] ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Terms of Service Content (HTML / Text)</label>
                            <textarea name="policy_terms_content" rows="6" 
                                      placeholder="Leave blank to use default corporate terms of service..." 
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#041536] focus:ring-1 focus:ring-[#041536] font-mono">{{ $settings['policy_terms_content'] ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Refund & Cancellation Policy Content (HTML / Text)</label>
                            <textarea name="policy_refund_content" rows="6" 
                                      placeholder="Leave blank to use default corporate refund policy..." 
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-xs focus:border-[#041536] focus:ring-1 focus:ring-[#041536] font-mono">{{ $settings['policy_refund_content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- ALL SITE PAGES, VISIBILITY CONTROLS & CUSTOM PAGE BUILDER -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'pages'" class="space-y-6">
                
                <!-- Part A: Built-in Public Pages Visibility Controls -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Page Access Control</span>
                            <h3 class="text-xl font-black text-[#051c45] mt-1">Built-In Public Pages Visibility</h3>
                            <p class="text-xs text-slate-500">Show or hide built-in platform pages from navigation menus and public access (admins can always preview hidden pages).</p>
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#c30102] hover:bg-[#a50102] text-white text-xs font-bold shadow-xs transition-colors">
                            Save Page States
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        <!-- Careers & Jobs -->
                        <div class="p-4 rounded-xl border flex items-center justify-between gap-3 transition-all" :class="pageJobsEnabled ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-[#051c45] truncate">Careers & Jobs Portal</div>
                                <div class="text-[11px] font-mono text-slate-400">/jobs</div>
                            </div>
                            <label class="inline-flex items-center gap-2 cursor-pointer shrink-0">
                                <input type="checkbox" x-model="pageJobsEnabled" class="w-4 h-4 accent-emerald-600 rounded">
                                <span class="text-[10px] font-black uppercase" :class="pageJobsEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="pageJobsEnabled ? 'Published' : 'Hidden'"></span>
                            </label>
                        </div>

                        <!-- Instructors Directory -->
                        <div class="p-4 rounded-xl border flex items-center justify-between gap-3 transition-all" :class="pageInstructorsEnabled ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-[#051c45] truncate">Instructors Directory</div>
                                <div class="text-[11px] font-mono text-slate-400">/instructors</div>
                            </div>
                            <label class="inline-flex items-center gap-2 cursor-pointer shrink-0">
                                <input type="checkbox" x-model="pageInstructorsEnabled" class="w-4 h-4 accent-emerald-600 rounded">
                                <span class="text-[10px] font-black uppercase" :class="pageInstructorsEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="pageInstructorsEnabled ? 'Published' : 'Hidden'"></span>
                            </label>
                        </div>

                        <!-- Blog & Knowledge Hub -->
                        <div class="p-4 rounded-xl border flex items-center justify-between gap-3 transition-all" :class="pageBlogEnabled ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-[#051c45] truncate">Knowledge Hub & Blogs</div>
                                <div class="text-[11px] font-mono text-slate-400">/blog</div>
                            </div>
                            <label class="inline-flex items-center gap-2 cursor-pointer shrink-0">
                                <input type="checkbox" x-model="pageBlogEnabled" class="w-4 h-4 accent-emerald-600 rounded">
                                <span class="text-[10px] font-black uppercase" :class="pageBlogEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="pageBlogEnabled ? 'Published' : 'Hidden'"></span>
                            </label>
                        </div>

                        <!-- Community Feed -->
                        <div class="p-4 rounded-xl border flex items-center justify-between gap-3 transition-all" :class="pageFeedEnabled ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-[#051c45] truncate">Community Discussion Feed</div>
                                <div class="text-[11px] font-mono text-slate-400">/feed</div>
                            </div>
                            <label class="inline-flex items-center gap-2 cursor-pointer shrink-0">
                                <input type="checkbox" x-model="pageFeedEnabled" class="w-4 h-4 accent-emerald-600 rounded">
                                <span class="text-[10px] font-black uppercase" :class="pageFeedEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="pageFeedEnabled ? 'Published' : 'Hidden'"></span>
                            </label>
                        </div>

                        <!-- About Us -->
                        <div class="p-4 rounded-xl border flex items-center justify-between gap-3 transition-all" :class="pageAboutEnabled ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-[#051c45] truncate">About Us Page</div>
                                <div class="text-[11px] font-mono text-slate-400">/about</div>
                            </div>
                            <label class="inline-flex items-center gap-2 cursor-pointer shrink-0">
                                <input type="checkbox" x-model="pageAboutEnabled" class="w-4 h-4 accent-emerald-600 rounded">
                                <span class="text-[10px] font-black uppercase" :class="pageAboutEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="pageAboutEnabled ? 'Published' : 'Hidden'"></span>
                            </label>
                        </div>

                        <!-- Contact & Support -->
                        <div class="p-4 rounded-xl border flex items-center justify-between gap-3 transition-all" :class="pageContactEnabled ? 'bg-white border-slate-200' : 'bg-slate-50 border-slate-200/60 opacity-75'">
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold text-[#051c45] truncate">Contact & Admissions</div>
                                <div class="text-[11px] font-mono text-slate-400">/contact</div>
                            </div>
                            <label class="inline-flex items-center gap-2 cursor-pointer shrink-0">
                                <input type="checkbox" x-model="pageContactEnabled" class="w-4 h-4 accent-emerald-600 rounded">
                                <span class="text-[10px] font-black uppercase" :class="pageContactEnabled ? 'text-emerald-600' : 'text-slate-400'" x-text="pageContactEnabled ? 'Published' : 'Hidden'"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Part B: Custom Standalone Pages Builder (/page/{slug}) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-50 text-blue-700 border border-blue-200">Custom Page Engine</span>
                            <h3 class="text-xl font-black text-[#051c45] mt-1">Custom Standalone Pages (`/page/{slug}`)</h3>
                            <p class="text-xs text-slate-500">Create unlimited custom pages (e.g., Corporate Training, Scholarships, Accreditation), publish or hide them, and attach them to the Header or Footer.</p>
                        </div>
                        <button type="button" @click="addCustomPage()" class="px-4 py-2.5 rounded-xl bg-[#051c45] hover:bg-[#0a2540] text-white text-xs font-bold inline-flex items-center gap-2 shadow-xs transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Create New Page</span>
                        </button>
                    </div>

                    <template x-if="customPages.length === 0">
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                            <i class="fas fa-file-circle-plus text-2xl text-slate-400 mb-2"></i>
                            <p class="text-sm font-bold text-slate-700">No custom standalone pages created yet.</p>
                            <p class="text-xs text-slate-500 mt-1 mb-4">Click "Create New Page" to launch a dedicated page with its own URL slug and navigation link.</p>
                            <button type="button" @click="addCustomPage()" class="px-4 py-2 rounded-xl bg-[#c30102] text-white text-xs font-bold">+ Create First Page</button>
                        </div>
                    </template>

                    <div class="space-y-4">
                        <template x-for="(cPage, pIdx) in customPages" :key="pIdx">
                            <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-4">
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 pb-3">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <span class="px-2.5 py-1 rounded-lg bg-[#051c45] text-white text-xs font-black" x-text="cPage.title || ('Page #' + (pIdx + 1))"></span>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-bold">
                                            <input type="checkbox" x-model="cPage.is_published" class="w-4 h-4 accent-emerald-600 rounded">
                                            <span :class="cPage.is_published ? 'text-emerald-700' : 'text-slate-400'" x-text="cPage.is_published ? 'Published' : 'Hidden (Draft)'"></span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700">
                                            <input type="checkbox" x-model="cPage.show_in_header" class="w-3.5 h-3.5 accent-[#051c45] rounded">
                                            <span>Header Nav</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-semibold text-slate-700">
                                            <input type="checkbox" x-model="cPage.show_in_footer" class="w-3.5 h-3.5 accent-[#051c45] rounded">
                                            <span>Footer Link</span>
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a :href="'/page/' + (cPage.slug || '')" target="_blank" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-bold text-[#051c45] hover:bg-slate-100">
                                            Preview ↗
                                        </a>
                                        <button type="button" @click="removeCustomPage(pIdx)" class="text-xs font-bold text-rose-600 hover:text-rose-800 px-2.5 py-1 rounded-lg hover:bg-rose-50">
                                            <i class="fas fa-trash-can mr-1"></i>Delete
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Page Title</label>
                                        <input type="text" x-model="cPage.title" placeholder="e.g. Corporate Training Solutions" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-sm font-bold text-[#051c45]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">URL Slug (`/page/{slug}`)</label>
                                        <div class="flex items-center rounded-xl border border-slate-300 bg-white overflow-hidden">
                                            <span class="px-3 py-2 bg-slate-100 text-slate-500 text-xs font-mono border-r border-slate-200">/page/</span>
                                            <input type="text" x-model="cPage.slug" placeholder="corporate-training" class="flex-1 px-3 py-2 text-xs font-mono text-slate-800 outline-none">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Hero Subtitle</label>
                                        <input type="text" x-model="cPage.subtitle" placeholder="Executive summary displayed in the navy banner" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">SEO Meta Description</label>
                                        <input type="text" x-model="cPage.meta_description" placeholder="Optional SEO description for search engines" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Page Body Content (HTML or Text)</label>
                                    <textarea x-model="cPage.content" rows="5" placeholder="<h2>Section Heading</h2><p>Write your page content...</p>" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-mono"></textarea>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Part C: Master Route Directory -->
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800">Master Index</span>
                                <span class="text-xs text-gray-500 font-semibold">20+ Standardized Site Routes</span>
                            </div>
                            <h3 class="text-xl font-black text-gray-900">All Site Pages & Live Verification Directory</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Audit, inspect, and open every public portal, student learning module, and compliance policy live in real-time.</p>
                        </div>
                        <a href="{{ route('sitemap.xml') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 transition-colors self-start sm:self-auto">
                            <i class="fas fa-sitemap text-xs"></i>
                            <span>View XML Sitemap</span>
                        </a>
                    </div>

                    <!-- 1. Public Front-Facing Pages -->
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-[#041536] mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>1. Public Discovery & Catalog Pages (Indexable)</span>
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php
                                $allPublicPages = [
                                    ['name' => 'Home Landing Page', 'url' => url('/'), 'route' => '/', 'badge' => 'Homepage'],
                                    ['name' => 'Course Catalog', 'url' => route('catalog.index'), 'route' => '/catalog', 'badge' => 'Core Catalog'],
                                    ['name' => 'Certificate Programs Filter', 'url' => route('catalog.certificate'), 'route' => '/certificate-course', 'badge' => 'Filtered Track'],
                                    ['name' => 'Day Long Masterclasses Filter', 'url' => route('catalog.day_long'), 'route' => '/day-long-course', 'badge' => 'Filtered Track'],
                                    ['name' => 'Export – Import Category Portal', 'url' => route('catalog.category', 'export-import'), 'route' => '/category/export-import', 'badge' => 'Category'],
                                    ['name' => 'Executive Instructors & Faculty', 'url' => route('instructors.index'), 'route' => '/instructors', 'badge' => 'Directory'],
                                    ['name' => 'Career & Placement Opportunities', 'url' => route('jobs.index'), 'route' => '/jobs', 'badge' => 'Careers'],
                                    ['name' => 'Community Discussion Feed', 'url' => route('feed.index'), 'route' => '/feed', 'badge' => 'Community'],
                                    ['name' => 'Knowledge Hub & Publications', 'url' => route('blog.index'), 'route' => '/blog', 'badge' => 'Publications'],
                                    ['name' => 'Public Certificate Registry', 'url' => route('certificate.verify'), 'route' => '/verify-certificate', 'badge' => 'Verification'],
                                    ['name' => 'Student Wishlist & Saved Courses', 'url' => route('wishlist.index'), 'route' => '/wishlist', 'badge' => 'Wishlist'],
                                    ['name' => 'Institutional About Us', 'url' => route('about.index'), 'route' => '/about', 'badge' => 'About'],
                                    ['name' => 'Campus Coordinates & Contact', 'url' => route('contact.index'), 'route' => '/contact', 'badge' => 'Contact'],
                                ];
                            @endphp
                            @foreach($allPublicPages as $p)
                                <div class="p-3 bg-slate-50 hover:bg-slate-100/80 rounded-xl border border-slate-200/80 flex items-center justify-between gap-3 transition-colors">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-gray-900 truncate">{{ $p['name'] }}</span>
                                            <span class="text-[9px] px-1.5 py-0.5 rounded font-bold bg-white text-slate-600 border border-slate-200 shrink-0">{{ $p['badge'] }}</span>
                                        </div>
                                        <span class="text-[11px] font-mono text-slate-500 block truncate">{{ $p['route'] }}</span>
                                    </div>
                                    <a href="{{ $p['url'] }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-[#041536] hover:text-white border border-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-sm shrink-0 transition-all">
                                        <span>Open</span>
                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Legal Compliance Suite -->
                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="text-xs font-black uppercase tracking-wider text-[#041536] mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>2. Institutional Trust & Legal Compliance Pages</span>
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @php
                                $allLegalPages = [
                                    ['name' => 'Privacy Policy', 'url' => route('legal.privacy'), 'route' => '/privacy-policy'],
                                    ['name' => 'Terms of Service', 'url' => route('legal.terms'), 'route' => '/terms-of-service'],
                                    ['name' => 'Refund & Cancellation Policy', 'url' => route('legal.refund'), 'route' => '/refund-policy'],
                                ];
                            @endphp
                            @foreach($allLegalPages as $lp)
                                <div class="p-3 bg-slate-50 hover:bg-slate-100/80 rounded-xl border border-slate-200/80 flex items-center justify-between gap-3 transition-colors">
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-gray-900 block truncate">{{ $lp['name'] }}</span>
                                        <span class="text-[11px] font-mono text-slate-500 block truncate">{{ $lp['route'] }}</span>
                                    </div>
                                    <a href="{{ $lp['url'] }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-[#041536] hover:text-white border border-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-sm shrink-0 transition-all">
                                        <span>Open</span>
                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 3. Student Authenticated Learning Suite -->
                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="text-xs font-black uppercase tracking-wider text-[#041536] mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <span>3. Student Authenticated Learning Suite</span>
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php
                                $allStudentPages = [
                                    ['name' => 'Student Dashboard', 'url' => route('dashboard'), 'route' => '/dashboard', 'desc' => 'Enrolled courses & payments overview'],
                                    ['name' => 'Performance & Career Applications', 'url' => route('performance.index'), 'route' => '/performance', 'desc' => 'Job applications & completion metrics'],
                                    ['name' => 'Account Profile & Settings', 'url' => route('profile.edit'), 'route' => '/profile', 'desc' => 'User credentials & avatar management'],
                                    ['name' => 'Notification Preferences', 'url' => route('notifications.index'), 'route' => '/notifications', 'desc' => 'System alerts & announcements'],
                                ];
                            @endphp
                            @foreach($allStudentPages as $sp)
                                <div class="p-3 bg-slate-50 hover:bg-slate-100/80 rounded-xl border border-slate-200/80 flex items-center justify-between gap-3 transition-colors">
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-gray-900 block truncate">{{ $sp['name'] }}</span>
                                        <span class="text-[11px] font-mono text-slate-500 block truncate">{{ $sp['route'] }} • {{ $sp['desc'] }}</span>
                                    </div>
                                    <a href="{{ $sp['url'] }}" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-[#041536] hover:text-white border border-slate-200 text-slate-700 text-xs font-semibold inline-flex items-center gap-1 shadow-sm shrink-0 transition-all">
                                        <span>Open</span>
                                        <i class="fas fa-external-link-alt text-[10px]"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 12. TEMPLATE MODE & CUSTOM CODE -->
            <!-- ========================================== -->
            <div x-show="activeTab === 'mode'" class="space-y-6">
                
                <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                    <div>
                        <h4 class="font-black text-gray-900 text-base">Rendering Engine Mode</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Switch between the standard section-by-section dynamic layout or inject custom landing page HTML.</p>
                    </div>

                    <div>
                        <label for="landing_page_mode" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Mode</label>
                        <select id="landing_page_mode" name="landing_page_mode" x-model="mode" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">
                            <option value="default">Default Section-by-Section Template (Recommended)</option>
                            <option value="custom">Custom Raw HTML Code</option>
                        </select>
                    </div>

                    <div x-show="mode === 'custom'" class="pt-4 border-t border-gray-100 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Raw HTML / Blade Content</label>
                        <textarea name="landing_custom_html" rows="18" 
                                  placeholder="<div class='custom-landing-page'>...</div>" 
                                  class="w-full px-4 py-3 rounded-xl border border-gray-300 font-mono text-xs focus:border-[#041536] focus:ring-1 focus:ring-[#041536]">{{ $settings['landing_custom_html'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection
