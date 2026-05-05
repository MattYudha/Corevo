<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100 mb-2 tracking-tight">Sign in to your account</h2>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">Enter your email and password to continue.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5 relative z-10">
        @csrf
        
        <!-- Email Input -->
        <div class="space-y-1.5">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300" for="email">Email address</label>
            <input class="block w-full px-3.5 py-2.5 bg-surface-lightInput dark:bg-surface-base border border-surface-lightBorder dark:border-surface-border rounded-lg text-zinc-900 dark:text-zinc-100 text-sm placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200 shadow-sm"
                id="email" name="email" value="{{ old('email') }}" placeholder="name@aratech.co.id" required autofocus type="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 dark:text-red-400 text-sm font-medium" />
        </div>
        
        <!-- Password Input -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300" for="password">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-sm text-primary hover:text-primary-hover dark:text-primary-focus dark:hover:text-primary transition-colors duration-200 font-medium" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <div class="relative group">
                <input class="block w-full pl-3.5 pr-10 py-2.5 bg-surface-lightInput dark:bg-surface-base border border-surface-lightBorder dark:border-surface-border rounded-lg text-zinc-900 dark:text-zinc-100 text-sm placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200 shadow-sm"
                    id="password" name="password" placeholder="••••••••" required autocomplete="current-password" type="password" />
                <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300 transition-colors duration-200" type="button" onclick="togglePassword()">
                    <!-- Eye SVG -->
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <!-- Eye Off SVG (hidden by default) -->
                    <svg id="eye-off-icon" class="hidden" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                        <line x1="2" y1="2" x2="22" y2="22"></line>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 dark:text-red-400 text-sm font-medium" />
        </div>
        
        <!-- Remember Me -->
        <div class="flex items-center pt-1 pb-2">
            <div class="relative flex items-center">
                <input class="peer h-4 w-4 appearance-none rounded border border-zinc-300 dark:border-surface-border bg-white dark:bg-surface-base checked:bg-primary dark:checked:bg-primary focus:ring-2 focus:ring-primary focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-surface-base transition-all duration-200 cursor-pointer"
                    id="remember_me" name="remember" type="checkbox" />
            </div>
            <label class="ml-2.5 block text-sm text-zinc-600 dark:text-zinc-400 cursor-pointer select-none" for="remember_me">
                Keep me signed in
            </label>
        </div>
        
        <!-- Submit Button -->
        <div>
            <button class="w-full flex justify-center items-center gap-2 py-2.5 px-4 rounded-lg font-medium text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary focus:ring-offset-white dark:focus:ring-offset-surface-base transition-all duration-200 shadow-sm dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.2)]" type="submit">
                Sign In
            </button>
        </div>
    </form>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>