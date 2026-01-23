<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerSessionExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tableCode = $request->route('table');
        $sessionKey = 'customer_session_started_at_'.$tableCode;
        $sessionLifetime = config('pos.customer_session_lifetime', 30);

        $sessionStartedAt = session($sessionKey);

        if ($sessionStartedAt) {
            $secondsElapsed = now()->diffInSeconds($sessionStartedAt);
            $lifetimeInSeconds = $sessionLifetime * 60;

            if ($secondsElapsed >= $lifetimeInSeconds) {
                // Clear customer-related session data
                session()->forget($sessionKey);
                session()->forget('cart');

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'expired' => true,
                        'message' => 'Your session has expired. Please scan the QR code again.',
                    ], 419);
                }

                return response()->view('customer.expired', [
                    'tableCode' => $tableCode,
                ], 419);
            }
        } else {
            // First visit - set the session start time
            session([$sessionKey => now()]);
        }

        return $next($request);
    }
}
