<?php

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/posts/*')) {
                return response()->json([
                    'message' => 'Post not found.'
                ], 404);
            }
        });
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/posts/*')) {
                if ($request->isMethod('DELETE') ||
                        $request->isMethod('PUT') ||
                            $request->isMethod('PATCH')) {
                    return response()->json([
                        'message' => 'Você não é o criador do Post.'
                    ], 403);
                }
            }
        });
    })->create();