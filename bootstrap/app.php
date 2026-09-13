<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin'          => \App\Http\Middleware\AdminMiddleware::class,
            'pengawas'       => \App\Http\Middleware\PengawasMiddleware::class,
            'single.session' => \App\Http\Middleware\SingleSessionMiddleware::class,
            'webauthn'       => \App\Http\Middleware\WebAuthnVerified::class,
        ]);

        // Terapkan pengecekan sesi tunggal ke semua request web yang sudah auth
        $middleware->appendToGroup('web', \App\Http\Middleware\SingleSessionMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\WebAuthnVerified::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi Anda telah berakhir.'], 419);
            }
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        });
    })->create();
