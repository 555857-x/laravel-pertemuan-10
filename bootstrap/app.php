<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Laravel 12 tidak punya Kernel.php, alias middleware didaftarkan di sini.
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // P12: semua error di /api/* dijawab JSON, bukan halaman HTML.
        // Tanpa ini, 404 dan 422 di API malah mengembalikan tampilan error Laravel.
        $exceptions->shouldRenderJsonWhen(fn (Request $request, \Throwable $e) =>
            $request->is('api/*') || $request->expectsJson());
    })->create();
