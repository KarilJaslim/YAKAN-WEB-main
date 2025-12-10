<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FabricType;
use Illuminate\Support\Facades\DB;

class FabricTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fabrics = [
            [
                'name' => 'Cotton',
                'description' => 'Soft, breathable, and versatile fabric perfect for everyday clothing and home decor',
                'base_price_per_meter' => 250.00,
                'material_composition' => '100% Cotton',
                'weight_gsm' => 180,
                'texture' => 'Smooth',
                'typical_uses' => ['clothing', 'home_decor', 'crafts'],
                'care_instructions' => 'Machine wash cold, tumble dry low, iron on medium heat',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Silk',
                'description' => 'Luxurious, smooth fabric with natural sheen, ideal for special occasion garments',
                'base_price_per_meter' => 850.00,
                'material_composition' => '100% Silk',
                'weight_gsm' => 120,
                'texture' => 'Smooth',
                'typical_uses' => ['clothing'],
                'care_instructions' => 'Dry clean only, cool iron if needed',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Polyester Cotton Blend',
                'description' => 'Durable and wrinkle-resistant blend, great for daily wear and easy maintenance',
                'base_price_per_meter' => 320.00,
                'material_composition' => '65% Polyester, 35% Cotton',
                'weight_gsm' => 200,
                'texture' => 'Smooth',
                'typical_uses' => ['clothing', 'home_decor'],
                'care_instructions' => 'Machine wash warm, tumble dry medium, iron on low heat',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Linen',
                'description' => 'Lightweight and breathable fabric with natural texture, perfect for summer clothing',
                'base_price_per_meter' => 450.00,
                'material_composition' => '100% Linen',
                'weight_gsm' => 160,
                'texture' => 'Textured',
                'typical_uses' => ['clothing', 'home_decor'],
                'care_instructions' => 'Machine wash cold, line dry, iron while damp',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Canvas',
                'description' => 'Heavy-duty fabric ideal for bags, upholstery, and durable home decor items',
                'base_price_per_meter' => 380.00,
                'material_composition' => '100% Cotton Canvas',
                'weight_gsm' => 280,
                'texture' => 'Textured',
                'typical_uses' => ['home_decor', 'crafts'],
                'care_instructions' => 'Spot clean or dry clean, iron on medium heat',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Chiffon',
                'description' => 'Lightweight, sheer fabric perfect for overlays and delicate garments',
                'base_price_per_meter' => 420.00,
                'material_composition' => '100% Polyester',
                'weight_gsm' => 80,
                'texture' => 'Smooth',
                'typical_uses' => ['clothing'],
                'care_instructions' => 'Hand wash cold, line dry, cool iron',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Jersey Knit',
                'description' => 'Stretchy and comfortable knit fabric, perfect for t-shirts and casual wear',
                'base_price_per_meter' => 350.00,
                'material_composition' => '95% Cotton, 5% Spandex',
                'weight_gsm' => 220,
                'texture' => 'Smooth',
                'typical_uses' => ['clothing'],
                'care_instructions' => 'Machine wash cold, tumble dry low, do not iron',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Velvet',
                'description' => 'Luxurious fabric with soft pile, ideal for formal wear and upscale home decor',
                'base_price_per_meter' => 680.00,
                'material_composition' => '100% Polyester Velvet',
                'weight_gsm' => 260,
                'texture' => 'Textured',
                'typical_uses' => ['clothing', 'home_decor'],
                'care_instructions' => 'Dry clean only, brush with soft cloth to maintain pile',
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($fabrics as $fabric) {
            FabricType::create($fabric);
        }

        $this->command->info('Fabric types seeded successfully!');
    }
}
