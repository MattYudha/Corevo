<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Email not found in our system.'
        ]);

        // Check 1 minute cooldown
        $lastRequested = Cache::get('reset_otp_time_' . $request->email);
        
        if ($lastRequested) {
            // Convert time from cache to whole seconds (Timestamp)
            $waktuLalu = \Carbon\Carbon::parse($lastRequested)->getTimestamp();
            $waktuSekarang = now()->getTimestamp();
            $selisihDetik = $waktuSekarang - $waktuLalu;

            if ($selisihDetik < 60 && $selisihDetik >= 0) {
                $sisaWaktu = 60 - $selisihDetik;
                return back()
                    ->with('email', $request->email)
                    ->withErrors(['email' => "Please wait $sisaWaktu seconds before requesting a new OTP."]);
            }
        }

        // Generate a random 6-digit OTP
        $otp = rand(100000, 999999);

        // Store OTP and Request Time in cache (Valid for 10 minutes)
        Cache::put('reset_otp_' . $request->email, $otp, now()->addMinutes(10));
        Cache::put('reset_otp_time_' . $request->email, now(), now()->addMinutes(10));

        // Send OTP Email
        Mail::raw("Hello!\n\nThe OTP code to reset your Corevo HRIS account password is: $otp\n\nThis code is only valid for 10 minutes.\nIf you did not request a password reset, please ignore this email.", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Password Reset OTP Code - Corevo HRIS');
        });

        // 4. Redirect to the OTP input page
        return redirect()->route('password.otp.form')
            ->with('email', $request->email)
            ->with('status', 'A new OTP code has been successfully sent to your email.');
    }
}