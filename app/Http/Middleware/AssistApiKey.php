<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssistApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('assist.app_key');
        if (empty($expected)) {
            return $next($request);
        }

        $provided = $request->header('X-Assist-App-Key');
        if ($provided !== $expected) {
            return response()->json(['error' => 'Invalid Assist app key.'], 403);
        }

        return $next($request);
    }
}
