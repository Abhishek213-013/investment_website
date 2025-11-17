<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class InvestorAuthController extends Controller
{
    /**
     * Handle investor login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Add investor-specific authentication logic
        $credentials['user_type'] = 'investor';
        $credentials['is_active'] = true;

        Log::info('🔐 Investor login attempt', ['email' => $credentials['email']]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            $request->session()->regenerate();
            
            Log::info('✅ Investor authenticated', [
                'id' => $user->id,
                'email' => $user->email,
                'user_type' => $user->user_type
            ]);
            
            // Update last login timestamp
            \App\Models\User::where('id', $user->id)->update(['last_login_at' => now()]);

            Log::info('🎯 Redirecting to investor dashboard');
            
            // Return Inertia redirect instead of JSON
            return redirect()->intended('/investor/dashboard');
        }

        Log::warning('❌ Investor login failed', ['email' => $credentials['email']]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records for an active investor account.'
        ])->onlyInput('email');
    }

    /**
     * Handle investor logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}