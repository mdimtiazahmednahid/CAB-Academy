<section class="space-y-6">
    <!-- Active Session & Mobile-Friendly Sign Out Block -->
    <div class="pb-6 border-b border-slate-200/80">
        <header>
            <h2 class="text-lg font-extrabold text-[#063b96] flex items-center gap-2">
                <i class="fas fa-sign-out-alt text-[#c4040a] text-sm"></i>
                <span>{{ __('Session & Sign Out') }}</span>
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Sign out of your active ACS Academy session on this device safely.') }}
            </p>
        </header>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#063b96] hover:bg-[#082860] text-white text-xs sm:text-sm font-extrabold tracking-wide shadow-sm transition-all cursor-pointer">
                <i class="fas fa-power-off text-xs text-amber-300"></i>
                <span>{{ __('Sign Out of Account') }}</span>
            </button>
        </form>
    </div>

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
