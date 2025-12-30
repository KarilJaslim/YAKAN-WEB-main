<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$patterns = \App\Models\YakanPattern::all(['id', 'name', 'description']);

echo "Total Patterns: " . $patterns->count() . "\n\n";

foreach ($patterns as $pattern) {
    echo $pattern->id . ". " . $pattern->name . "\n";
    if ($pattern->description) {
        echo "   Description: " . substr($pattern->description, 0, 50) . "...\n";
    }
    echo "\n";
}
