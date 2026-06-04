<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Activitylog\Facades\Activity;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::validate($credentials)) {
            $user = User::where('email', $credentials['email'])->first();
            
            $otp = Otp::createForUser($user, 'login');
            
            try {
                Mail::to($user->email)->send(new OtpMail($user, $otp, 'login'));
            } catch (\Exception $e) {
                \Log::error('Failed to send OTP email: ' . $e->getMessage());
                return back()->withErrors([
                    'email' => 'Failed to send verification code. Please try again.',
                ])->onlyInput('email');
            }

            session(['otp_user_id' => $user->id, 'otp_remember' => $remember]);

            return redirect()->route('otp.verify.form')
                ->with('success', 'A verification code has been sent to your email address.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'gender'   => 'nullable|in:male,female,other',
            'terms'    => 'required|accepted',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'gender'   => $validated['gender'] ?? null,
        ]);

        Auth::login($user);

        // Merge guest session wishlist upon successful registration
        try {
            \App\Models\Wishlist::mergeSessionWishlist($user);
        } catch (\Exception $e) {
            \Log::error('Failed to merge guest session wishlist on register: ' . $e->getMessage());
        }

        // Send welcome email immediately
        try {
            Mail::to($user)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            \Log::error('Could not send welcome email: ' . $e->getMessage());
        }

        Activity::log('User registered')->causedBy($user);

        return redirect()->route('home')
            ->with('success', 'Welcome to ZYRA, ' . $user->name . '! Your account has been created successfully.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        activity()->causedBy(Auth::user())->log('User logged out');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        return back()->with('success', 'Password reset link has been sent to your email address.');
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm($token)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.reset-password', compact('token'));
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::where('email', $validated['email'])->first();
        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully. Please login.');
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->email))) {
            abort(403, 'Invalid verification link');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home')->with('info', 'Your email is already verified.');
        }

        $user->markEmailAsVerified();

        activity()->causedBy($user)->log('Email verified');

        return redirect()->route('home')
            ->with('success', 'Your email has been verified successfully!');
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('home')->with('info', 'Your email is already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent! Please check your email.');
    }

    /**
     * Check authentication status (API)
     */
    public function checkAuth()
    {
        if (Auth::check()) {
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id'    => Auth::id(),
                    'name'  => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
            ]);
        }

        return response()->json(['authenticated' => false]);
    }

    /**
     * Show OTP verification form
     */
    public function showOtpVerificationForm()
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        $latestOtp = Otp::where('user_id', $userId)
            ->where('type', 'login')
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        $timeLeft = $latestOtp ? now()->diffInSeconds($latestOtp->expires_at) : 0;

        return view('auth.verify-otp', compact('timeLeft'));
    }

    /**
     * Verify OTP and complete login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = session('otp_user_id');
        $remember = session('otp_remember', false);

        if (!$userId) {
            return back()->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);
        if (!$user) {
            session()->forget(['otp_user_id', 'otp_remember']);
            return redirect()->route('login')->withErrors(['email' => 'Invalid session. Please login again.']);
        }

        $otp = Otp::verify($user, $request->otp, 'login');

        if (!$otp) {
            return back()->withErrors(['otp' => 'Invalid or expired verification code.'])->withInput();
        }

        Auth::login($user, $remember);
        
        // Merge guest session wishlist upon successful OTP verification login
        try {
            \App\Models\Wishlist::mergeSessionWishlist($user);
        } catch (\Exception $e) {
            \Log::error('Failed to merge guest session wishlist on OTP login: ' . $e->getMessage());
        }

        $request->session()->regenerate();
        session()->forget(['otp_user_id', 'otp_remember']);

        Activity::log('User logged in with 2FA')->causedBy(Auth::user());

        return redirect()->intended(route('home'))
            ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user) {
            session()->forget(['otp_user_id', 'otp_remember']);
            return redirect()->route('login');
        }

        $otp = Otp::createForUser($user, 'login');

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp, 'login'));
        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP email: ' . $e->getMessage());
            return back()->withErrors(['otp' => 'Failed to resend verification code. Please try again.']);
        }

        return back()->with('success', 'A new verification code has been sent to your email address.');
    }

    /**
     * Cancel OTP verification
     */
    public function cancelOtpVerification()
    {
        session()->forget(['otp_user_id', 'otp_remember']);
        return redirect()->route('login');
    }
}
