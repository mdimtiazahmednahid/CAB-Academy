@extends('layouts.admin')

@section('header_title', 'Write New Article')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.blogs.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-900 flex items-center gap-1.5">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Articles</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-200/80 shadow-sm">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
            <div class="w-10 h-10 rounded-2xl bg-[#051c45] text-[#10b981] flex items-center justify-center text-lg">
                <i class="fas fa-feather-pointed"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Write Knowledge Article</h1>
                <p class="text-xs text-gray-500">Publish regulatory insights, customs circulars, and trade research.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs space-y-1">
                @foreach($errors->all() as $error)
                    <div>&bull; {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.blogs.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Article Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full text-xs rounded-xl border-gray-300 focus:ring-[#051c45] p-3" placeholder="e.g. Navigating Bangladesh Customs SRO 2026: Key Changes for Importers">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Program Category *</label>
                    <input list="categoryList" name="category" value="{{ old('category', 'Customs & SRO') }}" required class="w-full text-xs rounded-xl border-gray-300 focus:ring-[#051c45] p-3" placeholder="Choose or type category">
                    <datalist id="categoryList">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}">
                        @endforeach
                        <option value="Export-Import">
                        <option value="Customs & SRO">
                        <option value="VAT & Tax">
                        <option value="Supply Chain">
                        <option value="Trade Finance">
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Brief Summary / Excerpt *</label>
                <textarea name="excerpt" rows="2" required class="w-full text-xs rounded-xl border-gray-300 focus:ring-[#051c45] p-3 leading-relaxed" placeholder="A concise 2-sentence summary that appears on blog cards and Google search previews...">{{ old('excerpt') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-2">SEO Meta Description (Optional)</label>
                <input type="text" name="meta_description" value="{{ old('meta_description') }}" class="w-full text-xs rounded-xl border-gray-300 focus:ring-[#051c45] p-3" placeholder="Search engine snippet preview...">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Article Content (Markdown / HTML Supported) *</label>
                <textarea name="content" rows="14" required class="w-full text-xs rounded-xl border-gray-300 focus:ring-[#051c45] p-3 font-mono leading-relaxed" placeholder="Write full article body here...">{{ old('content') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Author Name *</label>
                    <input type="text" name="author_name" value="{{ old('author_name', auth()->user()->name) }}" required class="w-full text-xs rounded-xl border-gray-300 focus:ring-[#051c45] p-3">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Author Title</label>
                    <input type="text" name="author_title" value="{{ old('author_title', 'Senior Trade Consultant') }}" class="w-full text-xs rounded-xl border-gray-300 focus:ring-[#051c45] p-3">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Read Time</label>
                    <input type="text" name="read_time" value="{{ old('read_time', '5 min read') }}" class="w-full text-xs rounded-xl border-gray-300 focus:ring-[#051c45] p-3">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Upload Featured Image (File)</label>
                    <input type="file" id="create_blog_img_input" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-white file:text-gray-700 file:border file:border-gray-200 hover:file:bg-gray-50">
                    <input type="hidden" name="featured_image_base64" id="create_featured_image_base64">
                    <script>
                        document.getElementById('create_blog_img_input')?.addEventListener('change', function(e) {
                            const file = e.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function(evt) {
                                    document.getElementById('create_featured_image_base64').value = evt.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    </script>
                    <p class="text-[10px] text-gray-400 mt-1">Recommended size: 1200x630 (PNG/JPG)</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-2">Or Direct Image URL</label>
                    <input type="text" name="featured_image" value="{{ old('featured_image') }}" class="w-full text-xs rounded-xl border-gray-300 focus:ring-[#051c45] p-2.5" placeholder="https://... or images/faculty/...">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-gray-300 text-[#051c45] focus:ring-[#051c45]">
                        <span class="text-xs font-bold text-gray-700">Publish immediately to Knowledge Hub</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <span class="text-xs font-bold text-amber-800">Feature on Homepage Knowledge Section</span>
                    </label>
                </div>

                <button type="submit" class="px-6 py-3 bg-[#051c45] hover:bg-[#072559] text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-blue-950/20">
                    Publish Article
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
