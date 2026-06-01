<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // Block registration with admin email domain
        if (str_ends_with(strtolower($request->email), '@admin.com')) {
            return back()->withErrors(['email' => 'Pendaftaran dengan domain email ini tidak diperbolehkan.'])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect('/');
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email','password'))) {
            return redirect('/');
        }

        return back()->with('error', 'Surel atau kata sandi yang Anda masukkan salah.');
    }
}
