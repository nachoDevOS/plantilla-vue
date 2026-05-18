<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAdminArea = $request->is('admin')
            || $request->is('admin/*')
            || $request->is('dashboard')
            || $request->is('settings/*');
        $cookieName = $isAdminArea ? 'appearance_admin' : 'appearance_public';
        $appearance = $request->cookie($cookieName);

        if ($isAdminArea) {
            $appearance ??= $request->cookie('appearance');
        }

        View::share('appearance', $appearance ?? 'system');

        return $next($request);
    }
}
