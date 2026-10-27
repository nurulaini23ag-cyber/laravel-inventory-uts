<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return Auth::user()->isCustomer() ? redirect()->route('orders.catalog') : redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            if (Auth::user()->isCustomer()) {
                return redirect()->route('orders.catalog')->with('success', 'Selamat datang, ' . Auth::user()->name . '! Silakan melihat katalog dan melakukan pemesanan barang.');
            }
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'login' => 'Kredensial yang dimasukkan tidak cocok dengan data kami.',
        ])->onlyInput('login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return Auth::user()->isCustomer() ? redirect()->route('orders.catalog') : redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'username.unique'    => 'Username ini sudah digunakan, silakan pilih username lain.',
            'email.unique'       => 'Email ini sudah terdaftar, silakan gunakan email lain atau login.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal harus 6 karakter.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => strtolower($request->username),
            'email'    => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role'     => 'user', // Otomatis mendaftar sebagai User / Pelanggan
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('orders.catalog')
            ->with('success', 'Pendaftaran akun berhasil! Selamat datang, ' . $user->name . '. Anda dapat langsung memesan produk aksesoris laptop favorit Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
