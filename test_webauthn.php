<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/webauthn/register/options', 'GET');

// Fake login
$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

$response = $kernel->handle($request);

echo "STATUS: " . $response->getStatusCode() . "\n";
echo "BODY: " . substr($response->getContent(), 0, 200) . "\n";
