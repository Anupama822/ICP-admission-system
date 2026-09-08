<?php

namespace App\Http\Controllers;

use App\Models\AdmissionYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                if (!AdmissionYear::where('is_active', true)->exists()) {
                    return redirect()->route('admission-year.setup');
                }

                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('staff.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // CRITICAL REQUIREMENT: Block inactive users from logging in
        if ($user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is currently inactive. Please contact the administrator.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if (!AdmissionYear::where('is_active', true)->exists()) {
            return redirect()->route('admission-year.setup')->with('status', 'Please set up the active admission year to continue.');
        }

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))->with('status', 'Welcome back, ' . $user->name . '!');
        }

        return redirect()->intended(route('staff.dashboard'))->with('status', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out successfully.');
    }
}
