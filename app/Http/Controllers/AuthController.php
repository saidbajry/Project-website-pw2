<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private const HOME = ['customer' => 'pelanggan.dashboard', 'staff' => 'sim.dashboard', 'owner' => 'eksekutif.dashboard'];

    public function form(string $portal)
    {
        return view('auth.login', ['portal' => $portal]);
    }

    public function login(Request $r, string $portal)
    {
        $c = $r->validate(['email' => 'required|email', 'password' => 'required']);

        if (Auth::attempt($c, $r->boolean('remember'))) {
            if (Auth::user()->role === $portal) {
                $r->session()->regenerate();
                return redirect()->intended(route(self::HOME[$portal]));
            }
            Auth::logout();
        }
        return back()->withErrors(['email' => 'Email atau kata sandi salah, atau akun ini bukan untuk portal ini.'])->onlyInput('email');
    }

    public function registerForm()
    {
        return view('auth.daftar');
    }

    public function register(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|min:6|confirmed',
        ]);
        $user = User::create($d + ['role' => 'customer']);
        Auth::login($user);
        $r->session()->regenerate();
        return redirect()->route('pelanggan.dashboard')->with('ok', 'Akun dibuat. Tambahkan kendaraan Anda ke garasi digital.');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('katalog');
    }
}
