<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SaveLastNonAdminUrl
{
    public function handle(Request $request, Closure $next)
    {
        // Only save GET requests that are not AJAX/JSON, not admin pages, not livewire, and not chat/api endpoints
        if ($request->isMethod('GET') 
            && !$request->ajax() 
            && !$request->expectsJson() 
            && !$request->is('admin*') 
            && !$request->is('livewire*') 
            && !$request->is('chat*') 
            && !$request->is('api*')
        ) {
            session(['last_non_admin_url' => $request->fullUrl()]);
        }

        return $next($request);
    }
}
