<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Create an account</h2>
        <p class="text-gray-500">Join our community of learners today.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                placeholder="e.g. Jane Doe">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                placeholder="Enter your email">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" 
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-500" />
        </div>

        @php
            $defaultRegistrationFields = [
                ['name' => 'job_role', 'label' => 'Current Job Role', 'type' => 'select', 'options' => 'Developer, Manager, Designer, Marketing, Other', 'is_mandatory' => true],
                ['name' => 'industry', 'label' => 'Industry', 'type' => 'select', 'options' => 'Technology, Finance, Healthcare, Retail, Education', 'is_mandatory' => false],
                ['name' => 'primary_goal', 'label' => 'Primary Goal', 'type' => 'select', 'options' => 'Upskill, Career Change, Team Training', 'is_mandatory' => true]
            ];
            
            $savedRegistrationFields = \App\Models\Setting::getVal('registration_fields');
            $registrationFields = $savedRegistrationFields ? json_decode($savedRegistrationFields, true) : $defaultRegistrationFields;
        @endphp

        @if(!empty($registrationFields))
            <div class="pt-4 mt-4 border-t border-gray-100 space-y-5">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Professional Profile</h3>
                @foreach($registrationFields as $field)
                    <div>
                        <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $field['label'] }}
                            @if($field['is_mandatory'])
                                <span class="text-red-500">*</span>
                            @else
                                <span class="text-gray-400 font-normal text-xs ml-1">(Optional)</span>
                            @endif
                        </label>
                        
                        @if($field['type'] === 'select')
                            <select id="{{ $field['name'] }}" name="preferences[{{ $field['name'] }}]" {{ $field['is_mandatory'] ? 'required' : '' }}
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-white">
                                <option value="">Select an option</option>
                                @foreach(explode(',', $field['options']) as $option)
                                    @php $opt = trim($option); @endphp
                                    @if(!empty($opt))
                                        <option value="{{ $opt }}" {{ old('preferences.'.$field['name']) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <input id="{{ $field['name'] }}" type="text" name="preferences[{{ $field['name'] }}]" value="{{ old('preferences.'.$field['name']) }}" {{ $field['is_mandatory'] ? 'required' : '' }}
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none" 
                                placeholder="Enter your answer">
                        @endif
                        <x-input-error :messages="$errors->get('preferences.'.$field['name'])" class="mt-2 text-sm text-red-500" />
                    </div>
                @endforeach
            </div>
        @endif

        <button type="submit" class="w-full bg-primary text-white font-bold py-3 px-4 rounded-xl hover:opacity-90 transition-opacity focus:ring-4 focus:ring-primary/30 outline-none transform active:scale-[0.98] mt-2">
            Create Account
        </button>

        <div class="text-center mt-6">
            <p class="text-gray-500 text-sm">
                Already have an account? 
                <a href="{{ route('login') }}" class="font-bold text-primary hover:text-primary-600 transition-colors">Sign in</a>
            </p>
        </div>
    </form>
</x-guest-layout>
