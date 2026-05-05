<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">OTP Verification</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            We have sent a 6-digit OTP code to the email <br> <span class="font-semibold text-indigo-600">{{ $email }}</span>
        </p>
    </div>

    <!-- Alert -->
    @if (session('status'))
        <div class="mb-4 bg-green-50 text-green-700 border border-green-200 p-3 rounded-md text-sm text-center font-semibold">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-md text-sm text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- OTP VERIFICATION -->
    <form method="POST" action="{{ route('password.otp.verify') }}" id="otp-form">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="otp" id="otp-hidden" required>

        <!-- 6-Digit OTP Boxes -->
        <div class="flex justify-center gap-2 sm:gap-3 mb-8" dir="ltr">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" maxlength="1" class="otp-input w-12 h-14 sm:w-14 sm:h-16 text-center text-2xl font-extrabold text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 outline-none transition-all dark:bg-gray-800 dark:border-gray-700 dark:text-white" inputmode="numeric" pattern="[0-9]*">
            @endfor
        </div>

        <div class="flex flex-col gap-4">
            <x-primary-button class="w-full justify-center py-3 text-lg">
                {{ __('Verify Code') }}
            </x-primary-button>
        </div>
    </form> 

    <!-- FORM 2: RESEND OTP -->
    <div class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
        Didn't receive the code? 
        <form method="POST" action="{{ route('password.email') }}" class="inline-block" id="resend-form">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" id="resend-btn" class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors bg-transparent border-none p-0 cursor-pointer underline decoration-transparent hover:decoration-indigo-800 disabled:opacity-50 disabled:cursor-not-allowed disabled:no-underline">
                Resend
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.otp-input');
            const hiddenInput = document.getElementById('otp-hidden');

            inputs[0].focus();

            function updateHiddenInput() {
                hiddenInput.value = Array.from(inputs).map(inp => inp.value).join('');
            }

            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value !== '') {
                        this.value = this.value.slice(-1); 
                        if (index < inputs.length - 1) inputs[index + 1].focus();
                    }
                    updateHiddenInput();
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && this.value === '') {
                        if (index > 0) inputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                    if (pastedData) {
                        inputs.forEach((inp, i) => inp.value = pastedData[i] || '');
                        const nextFocus = Math.min(pastedData.length, 5);
                        inputs[nextFocus].focus();
                        updateHiddenInput();
                    }
                });
            });

            // 60 SECOND COUNTDOWN LOGIC
            const resendBtn = document.getElementById('resend-btn');
            const storageKey = 'otp_cooldown_{{ $email }}';
            let lastRequestTime = localStorage.getItem(storageKey);
            const hasErrors = {{ $errors->any() ? 'true' : 'false' }};

            // Set time when page first loads (without validation errors)
            if (!lastRequestTime && !hasErrors) {
                lastRequestTime = Date.now();
                localStorage.setItem(storageKey, lastRequestTime);
            }

            function updateCountdown() {
                if (!lastRequestTime) return;
                
                const now = Date.now();
                const diffInSeconds = Math.floor((now - parseInt(lastRequestTime)) / 1000);
                const timeLeft = 60 - diffInSeconds;

                if (timeLeft > 0) {
                    resendBtn.disabled = true;
                    resendBtn.textContent = `Wait ${timeLeft} seconds`;
                    setTimeout(updateCountdown, 1000); // Loop every 1 second
                } else {
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend';
                    localStorage.removeItem(storageKey); // Remove when done
                }
            }

            updateCountdown();

            // When resend is clicked, reset time in browser
            document.getElementById('resend-form').addEventListener('submit', function() {
                localStorage.setItem(storageKey, Date.now());
            });
        });
    </script>
</x-guest-layout>