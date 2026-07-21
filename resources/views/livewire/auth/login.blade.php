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

<div class="w-full">
    <!-- Outer ambient lighting and card container -->
    <div class="relative group">
        <!-- Subtle modern gradient aura glow -->
        <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-3xl blur-xl opacity-20 group-hover:opacity-35 transition duration-1000"></div>

        <div class="relative flex flex-col gap-8 w-full max-w-md mx-auto p-8 sm:p-10 bg-white/80 dark:bg-zinc-900/90 backdrop-blur-2xl rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 shadow-2xl shadow-zinc-950/10">
            
            <!-- Header Section with IT Helpdesk Ticket Badge -->
            <div class="space-y-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">LaraTicket</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Authenticate to track and manage support tickets</p>
                </div>
            </div>

            <!-- Live System Status indicator -->
            <x-auth-session-status class="text-sm border-l-4 border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 p-3 rounded-lg text-emerald-800 dark:text-emerald-200" :status="session('status')" />

            <form wire:submit="login" class="flex flex-col gap-6">
                <!-- Email -->
                <div class="space-y-1.5">
                    <flux:input 
                        wire:model="email" 
                        label="{{ __('Work Email') }}" 
                        type="email" 
                        placeholder="name@company.com" 
                        required 
                        autofocus 
                        class="bg-zinc-50/50 dark:bg-zinc-800/50"
                    />
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <flux:label>{{ __('Password') }}</flux:label>
                    <flux:input 
                        wire:model="password" 
                        type="password" 
                        placeholder="••••••••" 
                        required 
                        class="bg-zinc-50/50 dark:bg-zinc-800/50"
                    />
                </div>

                <flux:button variant="primary" type="submit" class="w-full h-11 text-base font-semibold shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/35 transition-all duration-200">
                    {{ __('Login') }}
                </flux:button>
            </form>

            <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800/80 text-center text-sm flex items-center justify-center gap-1.5">
                <span class="text-zinc-500 dark:text-zinc-400">Need access?</span>
                <x-text-link href="mailto:test091200@gmail.com" class="font-semibold text-cyan-600 dark:text-cyan-400 hover:underline">Request an account</x-text-link>
            </div>
        </div>
    </div>
</div>