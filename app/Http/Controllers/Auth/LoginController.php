<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password salah.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user->role || !$user->role->is_active) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'User belum memiliki role aktif.',
            ]);
        }

        return match ($user->role->code) {
            'superadmin' => redirect()->route('dashboard'),
            'kasir' => redirect()->route('dashboard'),
            'agen' => redirect()->route('dashboard'),
            'petugas_dermaga' => redirect()->route('dermaga.control-center'),
            default => tap(redirect()->route('login'), function () {
                Auth::logout();
            }),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}