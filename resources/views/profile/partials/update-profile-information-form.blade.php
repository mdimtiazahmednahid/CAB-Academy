<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#051c45]">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <x-input-label for="profile_picture_input" :value="__('Profile Picture')" />
                <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-white shadow-md bg-[#051c45] text-white flex items-center justify-center font-black text-2xl shrink-0">
                        <img id="form_profile_picture_preview" src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover {{ $user->hasCustomProfilePicture() ? '' : 'hidden' }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=051c45&color=fff';">
                        @if(!$user->hasCustomProfilePicture())
                            <span id="form_profile_picture_initial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 space-y-2">
                        <input type="file" name="profile_picture" id="profile_picture_input" accept="image/*" class="block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#051c45] file:text-white hover:file:bg-[#0a2540] file:cursor-pointer cursor-pointer bg-white rounded-xl border border-slate-200 p-1" />
                        <input type="hidden" name="profile_picture_base64" id="profile_picture_base64">
                        <p class="text-[11px] text-slate-500">Select a photo and click <strong>Save</strong> below — your avatar updates across the top bar, sidebar, and community feed immediately.</p>
                    </div>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                <x-input-label for="cover_photo_input" :value="__('Cover Photo (Optional)')" />
                <div id="form_cover_photo_wrapper" class="mt-2 mb-2 w-full max-w-2xl {{ $user->cover_photo ? '' : 'hidden' }}">
                    <img id="form_cover_photo_preview" src="{{ $user->cover_photo_url }}" alt="Cover" class="w-full h-32 md:h-44 rounded-xl object-cover shadow-sm border border-gray-200" onerror="this.style.display='none';">
                </div>
                <input type="file" name="cover_photo" id="cover_photo_input" accept="image/*" class="mt-1 block w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-[#051c45] hover:file:bg-slate-300 file:cursor-pointer cursor-pointer bg-white rounded-xl border border-slate-200 p-1" />
                <input type="hidden" name="cover_photo_base64" id="cover_photo_base64">
                <x-input-error class="mt-2" :messages="$errors->get('cover_photo')" />
            </div>
        </div>

        <script>
            document.getElementById('profile_picture_input')?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        document.getElementById('profile_picture_base64').value = evt.target.result;
                        const formPreview = document.getElementById('form_profile_picture_preview');
                        const formInitial = document.getElementById('form_profile_picture_initial');
                        if (formPreview) {
                            formPreview.src = evt.target.result;
                            formPreview.classList.remove('hidden');
                        }
                        if (formInitial) formInitial.classList.add('hidden');

                        const heroPreview = document.getElementById('hero_profile_avatar_img');
                        const heroInitial = document.getElementById('hero_profile_avatar_initial');
                        if (heroPreview) {
                            heroPreview.src = evt.target.result;
                            heroPreview.classList.remove('hidden');
                        }
                        if (heroInitial) heroInitial.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
            document.getElementById('cover_photo_input')?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        document.getElementById('cover_photo_base64').value = evt.target.result;
                        const wrapper = document.getElementById('form_cover_photo_wrapper');
                        const preview = document.getElementById('form_cover_photo_preview');
                        if (wrapper) wrapper.classList.remove('hidden');
                        if (preview) {
                            preview.src = evt.target.result;
                            preview.style.display = 'block';
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
