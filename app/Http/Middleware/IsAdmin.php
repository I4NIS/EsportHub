<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isAdmin()) {
            return ApiResponse::error(
                'Vous n\'avez pas les permissions pour effectuer cette action.',
                null,
                403
            )->toResponse($request);
        }

        return $next($request);
    }
}
