<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectTo(fn(Request $request) => null);
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (ValidationException $e, Request $request) {
            return ApiResponse::error('Données invalides.', $e->errors(), 422)->toResponse($request);
        });
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            return ApiResponse::error('Session expirée ou token manquant. Merci de vous reconnecter.', null, 401)->toResponse($request);
        });
        $exceptions->renderable(function (NotFoundHttpException $e, Request $request) {
            return ApiResponse::error('La ressource demandée n\'existe pas.', null, 404)->toResponse($request);
        });
        $exceptions->renderable(function (AccessDeniedHttpException $e, Request $request) {
            return ApiResponse::error('Vous n\'avez pas les permissions pour effectuer cette action.', null, 403)->toResponse($request);
        });
        $exceptions->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            return ApiResponse::error('Méthode HTTP non autorisée pour cette route.', null, 405)->toResponse($request);
        });
        $exceptions->renderable(function (\Throwable $e, Request $request) {
            if (config('app.debug')) {
                return null;
            }
            return ApiResponse::error('Une erreur interne est survenue.', null, 500)->toResponse($request);
        });
        
    })->create();