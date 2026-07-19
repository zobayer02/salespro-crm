<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordResetOtpController extends Controller
{
    private const OTP_EXPIRES_IN_MINUTES = 10;

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ],
        );

        Mail::to($validated['email'])->send(new PasswordResetOtpMail($code));

        $request->session()->put('password_reset_email', $validated['email']);
        $request->session()->forget('password_reset_verified');

        return redirect()
            ->route('password.otp')
            ->with('success', 'OTP sent! Check your email.');
    }

    public function verify(): View|RedirectResponse
    {
        $email = session('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-password-otp', [
            'email' => $email,
            'expiresInMinutes' => self::OTP_EXPIRES_IN_MINUTES,
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'digits:6'],
        ]);

        if (! $this->isValidOtp($validated['email'], $validated['otp'])) {
            return back()
                ->withErrors(['otp' => 'Invalid or expired verification code.'])
                ->withInput(['email' => $validated['email']]);
        }

        $request->session()->put('password_reset_email', $validated['email']);
        $request->session()->put('password_reset_verified', true);

        return redirect()->route('password.reset.form');
    }

    public function resetForm(): View|RedirectResponse
    {
        $email = session('password_reset_email');

        if (! $email || ! session('password_reset_verified') || ! $this->hasValidResetToken($email)) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    public function reset(Request $request): RedirectResponse
    {
        $email = session('password_reset_email');

        if (! $email || ! session('password_reset_verified') || ! $this->hasValidResetToken($email)) {
            return redirect()->route('password.request');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::query()
            ->where('email', $email)
            ->update(['password' => Hash::make($validated['password'])]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();
        $request->session()->forget(['password_reset_email', 'password_reset_verified']);

        return redirect()->route('password.reset.success');
    }

    public function success(): View
    {
        return view('auth.password-reset-success');
    }

    private function isValidOtp(string $email, string $otp): bool
    {
        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        return $reset
            && ! Carbon::parse($reset->created_at)->lt(now()->subMinutes(self::OTP_EXPIRES_IN_MINUTES))
            && Hash::check($otp, $reset->token);
    }

    private function hasValidResetToken(string $email): bool
    {
        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        return $reset
            && ! Carbon::parse($reset->created_at)->lt(now()->subMinutes(self::OTP_EXPIRES_IN_MINUTES));
    }
}
