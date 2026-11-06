<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/** Lupa password khusus pelanggan. Staf/owner mengganti password lewat menu Akun setelah login. */
class PasswordResetController extends Controller
{
    public function request()
    {
        return view('auth.lupa');
    }

    public function email(Request $r)
    {
        $r->validate(['email' => 'required|email']);
        Password::sendResetLink(['email' => $r->input('email'), 'role' => 'customer']);
        // Respons sama baik email terdaftar maupun tidak, agar akun tidak bisa ditebak.
        return back()->with('ok', 'Jika email terdaftar sebagai pelanggan, tautan reset kata sandi sudah dikirim. Cek kotak masuk atau folder spam.');
    }

    public function form(Request $r, string $token)
    {
        return view('auth.reset', ['token' => $token, 'email' => $r->query('email')]);
    }

    public function reset(Request $r)
    {
        $r->validate(['token' => 'required', 'email' => 'required|email', 'password' => 'required|min:8|confirmed']);

        $status = Password::reset(
            ['email' => $r->input('email'), 'password' => $r->input('password'), 'password_confirmation' => $r->input('password_confirmation'), 'token' => $r->input('token'), 'role' => 'customer'],
            function ($user, $password) {
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('ok', 'Kata sandi berhasil diganti. Silakan masuk.')
            : back()->withInput($r->only('email'))->withErrors(['email' => 'Tautan reset tidak valid atau sudah kedaluwarsa. Minta tautan baru.']);
    }
}
