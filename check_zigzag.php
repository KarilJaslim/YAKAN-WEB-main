<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pattern = \App\Models\YakanPattern::where('name', 'LIKE', '%zigzag%')->with('media')->first();

if ($pattern) {
    echo "Pattern: {$pattern->name}\n";
    echo "ID: {$pattern->id}\n";
    echo "Media Count: {$pattern->media->count()}\n";
    
    if ($pattern->media->isNotEmpty()) {
        foreach ($pattern->media as $media) {
            echo "  Path: {$media->path}\n";
            echo "  URL: {$media->url}\n";
            echo "  File exists (uploads): " . (file_exists(public_path('uploads/' . $media->path)) ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "  NO MEDIA\n";
    }
    
    echo "Has SVG data: " . (isset($pattern->pattern_data['svg']) ? 'YES' : 'NO') . "\n";
    if (isset($pattern->pattern_data['svg'])) {
        echo "SVG length: " . strlen($pattern->pattern_data['svg']) . " chars\n";
    }
} else {
    echo "Pattern not found\n";
}
