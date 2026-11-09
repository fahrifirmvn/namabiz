<?php

namespace App\Http\Middleware;

use App\Support\PremiumAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremiumUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! PremiumAccess::isPremium($request->user())) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This action requires a Premium subscription.',
                    'paywall' => true,
                ], 403);
            }

            return redirect()->route('upgrade')->with('paywall_triggered', true);
        }

        return $next($request);
    }
}
