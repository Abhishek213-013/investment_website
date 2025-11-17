<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Handle admin login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        Log::info('🔐 Admin login attempt', ['email' => $credentials['email']]);

        // Debug: Check if user exists
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            Log::warning('❌ User not found', ['email' => $credentials['email']]);
            return back()->withErrors([
                'email' => 'User not found.',
            ])->onlyInput('email');
        }

        Log::info('👤 User found', [
            'id' => $user->id,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'is_active' => $user->is_active,
        ]);

        // Check if user is active
        if (!$user->is_active) {
            Log::warning('🚫 User not active', ['email' => $credentials['email']]);
            return back()->withErrors([
                'email' => 'Account is not active.',
            ])->onlyInput('email');
        }

        // Check password manually for debugging
        $passwordMatch = Hash::check($credentials['password'], $user->password);
        Log::info('🔑 Password check', [
            'provided' => $credentials['password'],
            'stored_hash' => $user->password,
            'match' => $passwordMatch
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            Log::info('✅ User authenticated', [
                'id' => $user->id,
                'email' => $user->email,
                'user_type' => $user->user_type
            ]);
            
            // Check if user is admin or super admin
            if (!in_array($user->user_type, ['admin', 'super_admin'])) {
                Log::warning('🚫 Access denied - not admin', ['user_type' => $user->user_type]);
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. Administrator privileges required.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            // Update last login timestamp
            \App\Models\User::where('id', $user->id)->update(['last_login_at' => now()]);

            Log::info('🎯 Redirecting to admin dashboard');
            
            // Return Inertia redirect
            return redirect()->intended('/admin/dashboard');
        }

        Log::warning('❌ Admin login failed', [
            'email' => $credentials['email'],
            'reason' => 'Password mismatch or other auth issue'
        ]);
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records for an active administrator account.',
        ])->onlyInput('email');
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}