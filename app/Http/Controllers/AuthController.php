<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|ends_with:@mail.unej.ac.id|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);
        
        $role = str_contains($request->email, 'admin') ? 'admin' : 'user';

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat. Silakan login!');
    }


    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status == 'blocked') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Akun Anda telah DIBLOKIR oleh Admin. Silakan hubungi pihak kampus.');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang Admin!');
            } else {
                return redirect()->route('user.dashboard')->with('success', 'Login berhasil!');
            }
        }

        return back()->with('error', 'Email atau password salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout');
    }

    
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            if (!str_ends_with($googleUser->getEmail(), '@mail.unej.ac.id')) {
                return redirect()->route('login')->with('error', 'Hanya email Unej yang diperbolehkan.');
            }
            $user = User::where('email', $googleUser->getEmail())->first();
            if ($user && $user->status == 'blocked') {
                return redirect()->route('login')->with('error', 'Akun Google ini telah DIBLOKIR oleh Admin. Hubungi pihak kampus.');
            }
            if (!$user) {
                $role = str_contains($googleUser->getEmail(), 'admin') ? 'admin' : 'user';
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)),
                    'google_id' => $googleUser->getId(),
                    'role' => $role,
                    'email_verified_at' => now(), 
                ]);
            } else {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            }

            Auth::login($user);
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Login dengan Google berhasil! Selamat datang Admin!');
            } else {
                return redirect()->route('user.dashboard')->with('success', 'Login dengan Google berhasil!');
            }

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login dengan Google gagal: ' . $e->getMessage());
        }
    }
}
