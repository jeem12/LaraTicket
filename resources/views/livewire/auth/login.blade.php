<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
   // Inside your login() function in login.blade.php

    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        // 1. Attempt authentication
        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // 2. Multi-role check
        $user = Auth::user();
        
        // 3. Clear rate limiter and regenerate session
        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // 4. Redirect based on role
        if ($user->role === 'admin') {
            $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
        } else {
            $this->redirect(route('user.dashboard', absolute: false), navigate: true);
        }
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-8 w-full max-w-sm mx-auto p-6 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
    
    <div class="space-y-1">
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-white">Help Desk Access</h1>
        <p class="text-sm text-zinc-500">Sign in to manage your support requests</p>
    </div>

    <!-- Live System Status indicator -->
    <x-auth-session-status class="text-sm border-l-4 border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 p-3 rounded" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-5">
        <!-- Email -->
        <flux:input 
            wire:model="email" 
            label="{{ __('Work Email') }}" 
            type="email" 
            placeholder="name@company.com" 
            required 
            autofocus 
        />

        <!-- Password with refined link placement -->
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Password') }}</flux:label>
                @if (Route::has('password.request'))
                    <x-text-link href="{{ route('password.request') }}" class="text-xs">
                        {{ __('Forgot password?') }}
                    </x-text-link>
                @endif
            </div>
            <flux:input 
                wire:model="password" 
                type="password" 
                placeholder="••••••••" 
                required 
            />
        </div>

        <flux:checkbox wire:model="remember" label="{{ __('Keep me signed in') }}" />

        <flux:button variant="primary" type="submit" class="w-full h-10">
            {{ __('Authenticate') }}
        </flux:button>
    </form>

    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 text-center text-sm">
        <span class="text-zinc-500">Need access?</span>
        <x-text-link href="{{ route('register') }}" class="font-medium">Request an account</x-text-link>
    </div>
</div>