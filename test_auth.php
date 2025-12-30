<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = \App\Models\User::first();
    if ($user) {
        echo 'Test token created for: ' . $user->email . "\n";
        $token = $user->createToken('test-token')->plainTextToken;
        echo 'Token: ' . $token . "\n";
    } else {
        echo 'No users found\n';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
