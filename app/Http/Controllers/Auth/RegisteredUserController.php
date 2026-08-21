<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        $defaultRegistrationFields = [
            ['name' => 'job_role', 'type' => 'select', 'is_mandatory' => true],
            ['name' => 'industry', 'type' => 'select', 'is_mandatory' => false],
            ['name' => 'primary_goal', 'type' => 'select', 'is_mandatory' => true]
        ];
        
        $savedRegistrationFields = \App\Models\Setting::getVal('registration_fields');
        $registrationFields = $savedRegistrationFields ? json_decode($savedRegistrationFields, true) : $defaultRegistrationFields;

        if (!empty($registrationFields)) {
            foreach ($registrationFields as $field) {
                $rule = $field['is_mandatory'] ? 'required' : 'nullable';
                $validationRules['preferences.' . $field['name']] = [$rule, 'string', 'max:255'];
            }
        }

        $request->validate($validationRules);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'preferences' => $request->has('preferences') ? json_encode($request->input('preferences')) : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
