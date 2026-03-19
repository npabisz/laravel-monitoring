<?php

namespace Npabisz\LaravelMonitoring\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeMonitoring
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Gate::check('viewMonitoring')) {
            abort(403);
        }

        return $next($request);
    }
}
