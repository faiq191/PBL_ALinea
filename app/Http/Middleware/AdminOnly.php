<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Not logged in or not @admin.com → pretend route doesn't exist
        if (!$user || !str_ends_with(strtolower($user->email), '@admin.com')) {
            abort(403);
        }

        return $next($request);
    }
}
