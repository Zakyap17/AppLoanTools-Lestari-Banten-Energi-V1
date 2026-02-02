<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for login/logout routes
        if ($request->is('login') || $request->is('logout') || $request->is('keep-alive')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (Auth::check()) {
            $timeout = 60 * 60; // 1 hour in seconds
            $lastActivity = session('last_activity_time');

            // Check inactivity timeout
            if ($lastActivity && (time() - $lastActivity > $timeout)) {
                // Session has expired due to inactivity
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Your session has expired due to inactivity. Please login again.');
            }

            // DISABLED: Tab fingerprinting causing logout issues
            // Only use simple inactivity timeout for now

            // Optimization: Only update last_activity_time if > 2 minutes have passed
            // This prevents writing to the session/database on every single request
            if (!$lastActivity || (time() - $lastActivity > 120)) {
                session(['last_activity_time' => time()]);
            }
        }

        return $next($request);
    }
}
