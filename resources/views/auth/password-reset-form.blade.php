<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create New Password</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            OTP successfully verified! Please create your new password
        </p>
    </div>

    <form method="POST" action="{{ route('password.reset.submit') }}">
        @csrf

        <!-- New Password -->
        <div class="mt-4">
            <x-input-label for="password" value="New Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autofocus />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4 mb-6">
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-3">
            {{ __('Save New Password') }}
        </x-primary-button>
    </form>
</x-guest-layout>