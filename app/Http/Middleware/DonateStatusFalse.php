<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DonateStatusFalse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $data = $request->user();

        if($data->available_for_donation === false ){
            return response()->json([
                'success' => false,
                'message' => 'User status is not available'
            ]);
        }
        return $next($request);
    }
}
