<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
                ]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'redirect' => $this->getRedirectUrl($user),
                ]);
            }

            return $this->redirectBasedOnRole($user);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 422);
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole($user)
    {
        return redirect()->route($this->getRedirectRoute($user));
    }

    /**
     * Get redirect route name based on role
     */
    private function getRedirectRoute($user): string
    {
        return match($user->role) {
            'ketua' => 'ketua.dashboard',
            'bendahara' => 'bendahara.dashboard',
            'admin' => 'admin.dashboard',
            'anggota' => 'anggota.dashboard',
            default => 'login',
        };
    }

    /**
     * Get redirect URL based on role
     */
    private function getRedirectUrl($user): string
    {
        return route($this->getRedirectRoute($user));
    }
}
