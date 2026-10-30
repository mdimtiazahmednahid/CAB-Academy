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
            try {
                if (class_exists(\App\Support\DatabaseSchemaRepair::class)) {
                    \App\Support\DatabaseSchemaRepair::ensureSchema(true);
                }
            } catch (\Throwable $ignore) {}

            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => 'Service temporarily unavailable. Please try again.',
                ], 503);
            }
            return response()->view('errors.database', [], 503);
        });

        $exceptions->render(function (\PDOException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => 'Service temporarily unavailable. Please try again.',
                ], 503);
            }
            return response()->view('errors.database', [], 503);
        });
    })->create();
