<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatternTag;

class PatternTagSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            ['name' => 'Geometric', 'color' => '#3B82F6'],
            ['name' => 'Floral', 'color' => '#10B981'],
            ['name' => 'Abstract', 'color' => '#8B5CF6'],
            ['name' => 'Traditional', 'color' => '#DC2626'],
            ['name' => 'Modern', 'color' => '#F59E0B'],
            ['name' => 'Minimalist', 'color' => '#6B7280'],
            ['name' => 'Colorful', 'color' => '#EC4899'],
            ['name' => 'Monochrome', 'color' => '#1F2937'],
        ];

        foreach ($tags as $tag) {
            PatternTag::firstOrCreate(['slug' => strtolower(str_replace(' ', '-', $tag['name']))], $tag);
        }
    }
}
