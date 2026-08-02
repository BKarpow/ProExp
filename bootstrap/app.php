<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsurePhoneIsSet;
use App\Http\Middleware\CheckTelegramBinding;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\MetaUserDataMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '/rapi', // <--- ЗМІНЮЄМО ПРЕФІКС ТУТ
        health: '/up',
        

    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            '8570176831:AAFPx1F-2zhWhChbzPC12uZOJaNHcYmZ2wk'
        ]);
        $middleware->web(append: [
            CheckTelegramBinding::class,
            MetaUserDataMiddleware::class
        ]);
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
