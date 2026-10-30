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
            <div>
                <x-input-label for="profile_picture_input" :value="__('Profile Picture')" />
                @if($user->profile_picture || ($user->instructorProfile && $user->instructorProfile->avatar))
                    <div class="mt-2 mb-2">
                        <img src="{{ $user->profile_picture_url }}" alt="Profile" class="w-20 h-20 md:w-24 md:h-24 rounded-full object-cover shadow-sm border border-gray-200" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=051c45&color=fff';">
                    </div>
                @endif
                <input type="file" name="profile_picture" id="profile_picture_input" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-[#051c45] hover:file:bg-slate-200" />
                <input type="hidden" name="profile_picture_base64" id="profile_picture_base64">
                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
            </div>

            <div>
                <x-input-label for="cover_photo_input" :value="__('Cover Photo')" />
                @if($user->cover_photo)
                    <div class="mt-2 mb-2 w-full max-w-2xl">
                        <img src="{{ $user->cover_photo_url }}" alt="Cover" class="w-full h-32 md:h-48 rounded-lg object-cover shadow-sm border border-gray-200" onerror="this.style.display='none';">
                    </div>
                @endif
                <input type="file" name="cover_photo" id="cover_photo_input" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-[#051c45] hover:file:bg-slate-200" />
                <input type="hidden" name="cover_photo_base64" id="cover_photo_base64">
                <x-input-error class="mt-2" :messages="$errors->get('cover_photo')" />
            </div>
        </div>
        </div>

        <script>
            document.getElementById('profile_picture_input')?.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        document.getElementById('profile_picture_base64').value = evt.target.result;
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
