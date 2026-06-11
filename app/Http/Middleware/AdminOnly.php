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

        // Store the referrer URL if it's from the same host and not an admin URL
        $referer = $request->headers->get('referer');
        if ($referer) {
            $refererUrl = parse_url($referer);
            $requestUrl = parse_url($request->fullUrl());

            if (isset($refererUrl['host']) && $refererUrl['host'] === $requestUrl['host']) {
                $path = $refererUrl['path'] ?? '/';
                if (!str_starts_with($path, '/admin') 
                    && !str_starts_with($path, '/chat') 
                    && !str_starts_with($path, '/api') 
                    && !str_starts_with($path, '/livewire')
                ) {
                    $request->session()->put('admin_back_url', $referer);
                }
            }
        }

        return $next($request);
    }
}

