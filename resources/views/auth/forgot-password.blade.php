<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100 mb-2 tracking-tight">Reset Password</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5 relative z-10">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1.5">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300" for="email">Email address</label>
            <input class="block w-full px-3.5 py-2.5 bg-surface-lightInput dark:bg-surface-base border border-surface-lightBorder dark:border-surface-border rounded-lg text-zinc-900 dark:text-zinc-100 text-sm placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200 shadow-sm"
                id="email" name="email" value="{{ old('email') }}" placeholder="name@aratech.co.id" required autofocus type="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 dark:text-red-400 text-sm font-medium" />
        </div>

        <div class="pt-2">
            <button class="w-full flex justify-center items-center gap-2 py-2.5 px-4 rounded-lg font-medium text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary focus:ring-offset-white dark:focus:ring-offset-surface-base transition-all duration-200 shadow-sm dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.2)]" type="submit">
                Email Password Reset Link
            </button>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-primary hover:text-primary-hover dark:text-primary-focus dark:hover:text-primary transition-colors duration-200 inline-flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to login
            </a>
        </div>
    </form>
</x-guest-layout>
