<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Pattern Media ===\n\n";

$patterns = \App\Models\YakanPattern::with('media')->take(5)->get();

foreach ($patterns as $pattern) {
    echo "Pattern: {$pattern->name}\n";
    echo "  ID: {$pattern->id}\n";
    echo "  Media Count: {$pattern->media->count()}\n";
    
    if ($pattern->media->isNotEmpty()) {
        foreach ($pattern->media as $media) {
            echo "    Media ID: {$media->id}\n";
            echo "    Path: {$media->path}\n";
            echo "    URL: {$media->url}\n";
            echo "    File exists in uploads?: " . (file_exists(public_path('uploads/' . $media->path)) ? 'YES' : 'NO') . "\n";
            echo "    File exists in storage?: " . (file_exists(public_path('storage/' . $media->path)) ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "    NO MEDIA ATTACHED\n";
    }
    echo "\n";
}
