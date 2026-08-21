<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Notification Preferences') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Choose which notifications you want to receive.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.notifications.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        @php
            $prefs = auth()->user()->notification_preferences ?? [];
            if (is_string($prefs)) {
                $prefs = json_decode($prefs, true) ?? [];
            }
        @endphp

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="notify_new_courses" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ !isset($prefs['new_courses']) || $prefs['new_courses'] ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Notify me when new courses are published') }}</span>
            </label>
        </div>

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="notify_new_jobs" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ !isset($prefs['new_jobs']) || $prefs['new_jobs'] ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Notify me about new job opportunities') }}</span>
            </label>
        </div>

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="notify_announcements" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ !isset($prefs['announcements']) || $prefs['announcements'] ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Receive important announcements') }}</span>
            </label>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'notifications-updated')
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
