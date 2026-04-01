<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Log::info('AdminMiddleware: Checking admin access', [
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_role' => Auth::user()?->role,
            'user_is_active' => Auth::user()?->is_active,
        ]);
        
        // Check if user is authenticated
        if (!Auth::check()) {
            \Log::warning('AdminMiddleware: User not authenticated');
            return redirect()->route('login')
                ->with('error', 'Please log in to access admin panel.');
        }

        $user = Auth::user();

        // Check if user is admin
        if (!$user || !$user->isAdmin()) {
            \Log::warning('AdminMiddleware: User is not admin', ['user_role' => $user?->role]);
            abort(403, 'Unauthorized - Admin access required.');
        }

        // Check if user account is active
        if (!$user->isActive()) {
            \Log::warning('AdminMiddleware: User account is not active', ['user_id' => $user->id]);
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
