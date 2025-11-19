<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required',
            'password' => 'required'
        ]);

        $input = $request->input('login_id');
        $password = $request->input('password');

        // 1. EMERGENCY LOGIN (Defined in .env)
        // Add SYSTEM_ADMIN_USER and SYSTEM_ADMIN_PASS to your .env file
        $sysUser = env('SYSTEM_ADMIN_USER', 'system_admin');
        $sysPass = env('SYSTEM_ADMIN_PASS', 'system_admin_123');

        if ($input === $sysUser && $password === $sysPass) {
            // Check if a system admin user exists in DB, if not create a temporary one in memory
            // Note: Laravel Auth requires a persisted user for session persistence normally.
            // We will find the Super Admin (ID 1) and force login,
            // OR creating a dummy user if DB is completely empty.

            $user = User::where('admin_type', 'super_admin')->first();

            if (!$user) {
                // Fallback if DB is empty, Create one on the fly
                $user = User::create([
                    'full_name' => 'System Emergency Admin',
                    'email' => 'emergency@sys.local',
                    'password' => Hash::make($sysPass),
                    'admin_type' => 'super_admin',
                    'user_id' => 'SYS-EMERGENCY'
                ]);
            }

            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Logged in via Emergency Protocol');
        }

        // 2. STANDARD DB LOGIN
        // Determine if input is email or user_id
        $loginType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_id';

        $credentials = [
            $loginType => $input,
            'password' => $password
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check OTP Status
            if ($user->otp_status !== 'verified') {
                // Generate OTP here if needed
                // return redirect()->route('auth.otp.verify');

                // For now, verify automatically if not strictly enforced yet
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'login_id' => 'The provided credentials do not match our records.',
        ])->onlyInput('login_id');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
