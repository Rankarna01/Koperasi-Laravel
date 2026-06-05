<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin,bendahara')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            // Redirect to appropriate dashboard based on user's actual role
            $redirectRoute = match($user->role) {
                'ketua' => 'ketua.dashboard',
                'bendahara' => 'bendahara.dashboard',
                'admin' => 'admin.dashboard',
                'anggota' => 'anggota.dashboard',
                default => 'login',
            };

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke halaman ini.'
                ], 403);
            }

            return redirect()->route($redirectRoute)
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
