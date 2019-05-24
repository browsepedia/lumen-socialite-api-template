<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class JwtAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $credentials = JWT::decode(substr($token, 6), env('JWT_SECRET'));
        } catch (ExpiredException $ex) {
            return response()->json(['error' => 'An error occurred while reading token'], 500);
        }

        $user = User::find($credentials->sub);

        if (empty($user)) {
            return response()->json(['error' => 'Token is invalid',], 401);
        }

        return $next($request);
    }
}
