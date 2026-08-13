<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ErpApiAuth
{
    /**
     * Middleware de autenticación para los endpoints públicos.
     */
    private const AUTH_SECRET = 'ejercicio';

    public function handle(Request $request, Closure $next): Response
    {
        $expected = base64_encode(self::AUTH_SECRET);
        $provided = $request->input('authorization');

        if ($provided !== $expected) {
            return response()->json(['success' => 'authorization_fail']);
        }

        return $next($request);
    }
}
