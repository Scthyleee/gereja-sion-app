<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        $profil = DB::table('profil_gereja')->first();
        return view('auth.login', compact('profil'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check login attempts
        $ip = $request->ip();
        $attempt = DB::table('login_attempts')
            ->where('ip_address', $ip)
            ->where('email', $request->email)
            ->first();

        if ($attempt && $attempt->locked_until && now()->lt($attempt->locked_until)) {
            $remaining = now()->diffInSeconds($attempt->locked_until);
            return back()->withErrors(['email' => "Akun terkunci. Coba lagi dalam {$remaining} detik."])->withInput();
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Reset attempts on success
            DB::table('login_attempts')
                ->where('ip_address', $ip)
                ->where('email', $request->email)
                ->delete();

            $request->session()->regenerate();
            Auth::user()->update(['last_login' => now()]);

            return redirect()->intended(route('dashboard'));
        }

        // Increment failed attempts
        if ($attempt) {
            $newAttempts = $attempt->attempts + 1;
            $locked = $newAttempts >= 5 ? now()->addMinutes(3) : null;
            DB::table('login_attempts')
                ->where('id', $attempt->id)
                ->update(['attempts' => $newAttempts, 'locked_until' => $locked, 'last_attempt' => now()]);
        } else {
            DB::table('login_attempts')->insert([
                'ip_address' => $ip,
                'email' => $request->email,
                'attempts' => 1,
                'last_attempt' => now(),
            ]);
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
