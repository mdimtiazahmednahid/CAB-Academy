<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Supports login via Email Address OR 11-digit Mobile Number + Password/PIN.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim((string) $this->input('email'));
        $password = (string) $this->input('password');
        $remember = $this->boolean('remember');
        $authenticated = false;

        if (str_contains($identifier, '@')) {
            try {
                $authenticated = Auth::attempt(['email' => $identifier, 'password' => $password], $remember);
            } catch (\Throwable $e) {
                \App\Support\DatabaseSchemaRepair::ensureSchema(true);
                $authenticated = Auth::attempt(['email' => $identifier, 'password' => $password], $remember);
            }
        } else {
            $digits = preg_replace('/[^0-9]/', '', $identifier);
            if (strlen($digits) === 13 && str_starts_with($digits, '8801')) {
                $digits = substr($digits, 2);
            }

            if (!empty($digits)) {
                try {
                    $candidates = \App\Models\User::where('phone', $digits)
                        ->orWhere('phone', $identifier)
                        ->orWhere('email', 'student_' . $digits . '@cabacademy.com')
                        ->get();
                } catch (\Throwable $e) {
                    \App\Support\DatabaseSchemaRepair::ensureSchema(true);
                    $candidates = \App\Models\User::where('email', 'student_' . $digits . '@cabacademy.com')->get();
                }

                foreach ($candidates as $candidate) {
                    if (Auth::attempt(['email' => $candidate->email, 'password' => $password], $remember)) {
                        $authenticated = true;
                        break;
                    }
                    // Legacy Express Enrollment recovery: if account was auto-created (student_{phone}@cabacademy.com)
                    // and student logs in with last 4 digits of their phone number
                    if (
                        !$candidate->isAdmin()
                        && str_starts_with((string) $candidate->email, 'student_')
                        && strlen($digits) >= 4
                        && $password === substr($digits, -4)
                    ) {
                        $candidate->password = \Illuminate\Support\Facades\Hash::make($password);
                        $candidate->saveQuietly();
                        Auth::login($candidate, $remember);
                        $authenticated = true;
                        break;
                    }
                }
            }

            if (!$authenticated) {
                try {
                    $authenticated = Auth::attempt(['email' => $identifier, 'password' => $password], $remember);
                } catch (\Throwable $e) {
                    $authenticated = false;
                }
            }
        }

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
