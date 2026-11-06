<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function form()
    {
        return view('akun.password');
    }

    public function update(Request $r)
    {
        $r->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed|different:current_password',
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.different' => 'Kata sandi baru harus berbeda dari yang lama.',
        ]);
        $r->user()->update(['password' => $r->input('password')]);
        return back()->with('ok', 'Kata sandi berhasil diganti.');
    }
}
