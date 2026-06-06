<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // ← この1行を追加！
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 🔒 横線もエラーも出ない、Laravel 11以降の正しい書き方
        $middleware->validateCsrfTokens(except: [
            'api/books/store',
            'api/books/delete',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();