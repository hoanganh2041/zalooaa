<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Constants\ResCode;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    // Add new method 
    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
                'error_code' => ResCode::UNAUTHENTICATED,
                'message' => ResCode::UNAUTHENTICATED_MSG
            ], 401));
    }
}
