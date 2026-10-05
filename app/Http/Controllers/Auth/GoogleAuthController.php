<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user to Google OAuth provider.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with('error', 'Konfigurasi Google Login belum lengkap (GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET belum diisi di .env).');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback returned from Google OAuth provider.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::warning('Google OAuth callback error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Autentikasi Google dibatalkan atau mengalami kendala: ' . $e->getMessage());
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();

        if (empty($email)) {
            return redirect()->route('login')->with('error', 'Gagal mengambil alamat email dari akun Google Anda.');
        }

        // Case B: User already connected via Google ID
        $user = User::where('google_id', $googleId)->first();

        if ($user) {
            // Update avatar if available
            if ($googleUser->getAvatar() && empty($user->avatar)) {
                $user->update(['avatar' => $googleUser->getAvatar()]);
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Case C / D: Existing user with matching email (register with email/password previously)
        $user = User::where('email', $email)->first();

        if ($user) {
            // Link Google ID & avatar to existing user account
            $user->update([
                'google_id' => $googleId,
                'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false))
                ->with('status', 'Akun Anda berhasil ditautkan dengan Google.');
        }

        // Case A: New user from Google (Role is strictly 'anggota' / member)
        $newUser = User::create([
            'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Anggota RPK',
            'email' => $email,
            'google_id' => $googleId,
            'avatar' => $googleUser->getAvatar(),
            'role' => 'anggota',
            'email_verified_at' => now(),
            'password' => null,
        ]);

        event(new Registered($newUser));

        Auth::login($newUser, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false))
            ->with('status', 'Selamat datang di RPK PUSTAKA IMM SAINTEK MU! Akun Anda berhasil terdaftar melalui Google.');
    }
}
