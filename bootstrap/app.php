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
        // Keluarkan cookie device_uuid dari enkripsi agar bisa dibaca secara langsung dan andal
        $middleware->encryptCookies(except: ['device_uuid']);

        $middleware->alias([
            'admin'          => \App\Http\Middleware\AdminMiddleware::class,
            'pengawas'       => \App\Http\Middleware\PengawasMiddleware::class,
            'kurikulum'      => \App\Http\Middleware\KurikulumMiddleware::class,
            'single.session' => \App\Http\Middleware\SingleSessionMiddleware::class,
        ]);

        // Terapkan pengecekan sesi tunggal ke semua request web yang sudah auth
        $middleware->appendToGroup('web', \App\Http\Middleware\SingleSessionMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
