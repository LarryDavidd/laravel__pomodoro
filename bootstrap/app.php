<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Регистрация middleware
        $middleware->alias([
            'jwt.auth' => Tymon\JWTAuth\Http\Middleware\Authenticate::class,
            'jwt.refresh' => Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Обработка JWT исключений
        $exceptions->render(function (TokenInvalidException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Token is invalid'], 401);
            }
        });
        
        $exceptions->render(function (TokenExpiredException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Token has expired'], 401);
            }
        });
        
        $exceptions->render(function (JWTException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Token is absent'], 401);
            }
        });
    })->create();