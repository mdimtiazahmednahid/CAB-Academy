<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
        <p class="text-gray-500">Please enter your details to sign in.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                placeholder="Enter your email">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary hover:text-primary-600 transition-colors">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary transition-colors">
            <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                Remember me for 30 days
            </label>
        </div>

        <button type="submit" class="w-full bg-primary text-white font-bold py-3 px-4 rounded-xl hover:opacity-90 transition-opacity focus:ring-4 focus:ring-primary/30 outline-none transform active:scale-[0.98]">
            Sign In
        </button>
        
        <div class="text-center mt-6">
            <p class="text-gray-500 text-sm">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-bold text-primary hover:text-primary-600 transition-colors">Sign up</a>
            </p>
        </div>
    </form>
</x-guest-layout>
