<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }

    // Show OTP Input Page
    public function showOtpForm(Request $request)
    {
        // Get from session, or get from 'old input' if there was an error
        $email = session('email') ?? old('email') ?? $request->email;
        
        if (!$email) return redirect()->route('password.request');

        // Just in case the page is refreshed, keep the email from disappearing
        session()->now('email', $email);

        return view('auth.verify-otp', ['email' => $email]);
    }

    // Process OTP Verification Only
    public function verifyOtpOnly(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'numeric', 'digits:6'],
        ]);

        $cachedOtp = \Illuminate\Support\Facades\Cache::get('reset_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            // THE FIX IS HERE BRO: We return the email again using ->with('email', ...)
            return back()
                ->with('email', $request->email)
                ->withErrors(['otp' => 'Invalid or expired OTP code.']);
        }

        // If correct, save the email to the session indicating OTP has passed
        session(['otp_verified_email' => $request->email]);
        return redirect()->route('password.reset.form');
    }

    // Show Password Reset Form (Only accessible if OTP passed)
    public function showResetForm()
    {
        $email = session('otp_verified_email');
        if (!$email) return redirect()->route('password.request')->withErrors(['email' => 'Session expired, please request a new OTP code.']);

        return view('auth.password-reset-form.blade', ['email' => $email]);
    }

    // Process Saving the New Password
    public function updatePassword(Request $request)
    {
        $email = session('otp_verified_email');
        if (!$email) return redirect()->route('password.request');

        $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = \App\Models\User::where('email', $email)->first();
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        // Clear cache and session for security
        \Illuminate\Support\Facades\Cache::forget('reset_otp_' . $email);
        session()->forget('otp_verified_email');

        return redirect()->route('login')->with('status', 'Password changed successfully! Please login with your new password.');
    }
}