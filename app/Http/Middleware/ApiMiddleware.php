<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Passport\Exceptions\OAuthServerException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $key = 'rate_limit_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['error' => 'Too many requests. Try again later.'], 429);
        }
        RateLimiter::hit($key, 60);

        if (Auth::check()) {
            $user = Auth::user();
            $request->merge(['tenant_id' => $user->tenant_id]);
        }

        try {
            return $next($request);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Resource not found'], 404);
        } catch (ValidationException $exception) {
            return response()->json(['errors' => $exception->errors()], 422);
        } catch (AuthenticationException $exception) {
            return response()->json(['error' => 'Authentication required'], 401);
        } catch (OAuthServerException $exception) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }
}
