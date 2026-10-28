<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\Illuminate\Database\QueryException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => 'Database Query Error',
                    'message' => $e->getMessage(),
                ], 500);
            }
            return response()->view('errors.database', [
                'exception' => $e,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        });

        $exceptions->render(function (\PDOException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => 'Database Connection Error',
                    'message' => $e->getMessage(),
                ], 500);
            }
            return response()->view('errors.database', [
                'exception' => $e,
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        });
    })->create();
