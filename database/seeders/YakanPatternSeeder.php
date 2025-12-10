<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\YakanPattern;

class YakanPatternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patterns = [
            [
                'name' => 'Sinaluan',
                'description' => 'The most sacred Yakan wedding pattern featuring intricate eight-point star (bita) motifs with sacred checkerboard backgrounds. Each star contains 16 smaller stars representing the extended family members. The pattern includes traditional diamond borders and zigzag lightning motifs representing protection from evil spirits. Woven by master weavers using natural dyes - turmeric yellow, indigo blue, mangosteen red, and charcoal black.',
                'category' => 'traditional',
                'difficulty_level' => 'master',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="sinaluan" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="20" height="20" fill="#8B0000"/><rect x="20" y="20" width="20" height="20" fill="#8B0000"/><rect x="20" y="0" width="20" height="20" fill="#FFD700"/><rect x="0" y="20" width="20" height="20" fill="#FFD700"/><polygon points="10,5 15,8 15,12 10,15 5,12 5,8" fill="#4B0082" stroke="#FFFFFF" stroke-width="0.3"/><polygon points="30,5 35,8 35,12 30,15 25,12 25,8" fill="#4B0082" stroke="#FFFFFF" stroke-width="0.3"/><polygon points="10,25 15,28 15,32 10,35 5,32 5,28" fill="#4B0082" stroke="#FFFFFF" stroke-width="0.3"/><polygon points="30,25 35,28 35,32 30,35 25,32 25,28" fill="#4B0082" stroke="#FFFFFF" stroke-width="0.3"/><polygon points="10,8 12,10 10,12 8,10" fill="#FFFFFF" stroke="#4B0082" stroke-width="0.2"/><polygon points="30,8 32,10 30,12 28,10" fill="#FFFFFF" stroke="#4B0082" stroke-width="0.2"/><polygon points="10,28 12,30 10,32 8,30" fill="#FFFFFF" stroke="#4B0082" stroke-width="0.2"/><polygon points="30,28 32,30 30,32 28,30" fill="#FFFFFF" stroke="#4B0082" stroke-width="0.2"/><line x1="0" y1="0" x2="40" y2="40" stroke="#FFFFFF" stroke-width="0.5" opacity="0.4"/><line x1="40" y1="0" x2="0" y2="40" stroke="#FFFFFF" stroke-width="0.5" opacity="0.4"/><line x1="20" y1="0" x2="20" y2="40" stroke="#FFFFFF" stroke-width="0.3" opacity="0.5"/><line x1="0" y1="20" x2="40" y2="20" stroke="#FFFFFF" stroke-width="0.3" opacity="0.5"/><path d="M0,10 L5,10 L10,5 L10,0" fill="none" stroke="#FFFFFF" stroke-width="0.5" opacity="0.6"/><path d="M30,0 L30,5 L35,10 L40,10" fill="none" stroke="#FFFFFF" stroke-width="0.5" opacity="0.6"/><path d="M0,30 L5,30 L10,35 L10,40" fill="none" stroke="#FFFFFF" stroke-width="0.5" opacity="0.6"/><path d="M30,40 L30,35 L35,30 L40,30" fill="none" stroke="#FFFFFF" stroke-width="0.5" opacity="0.6"/></pattern></defs><rect width="100%" height="100%" fill="url(#sinaluan)"/></svg>',
                    'complexity_score' => 10
                ],
                'base_color' => '#8B0000',
                'color_variations' => ['#8B0000,#FFD700,#4B0082,#FFFFFF', '#4B0082,#FFD700,#8B0000,#FFFFFF', '#228B22,#FFD700,#8B0000,#FFFFFF'],
                'base_price_multiplier' => 2.00,
                'popularity_score' => 25,
            ],
            [
                'name' => 'Bunga Sama',
                'description' => 'Authentic Yakan floral pattern featuring sacred ylang-ylang and sampaguita flowers with eight petals each, representing the eight directions of Yakan cosmology. Each flower contains a central star motif and is surrounded by smaller flower buds. The pattern includes traditional leaf vines and diamond connectors that symbolize the interconnectedness of all living things in Basilan.',
                'category' => 'traditional',
                'difficulty_level' => 'expert',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="bunga" x="0" y="0" width="35" height="35" patternUnits="userSpaceOnUse"><circle cx="17.5" cy="17.5" r="10" fill="#FF6347" stroke="#8B0000" stroke-width="1.5"/><circle cx="17.5" cy="17.5" r="3" fill="#FFD700"/><circle cx="17.5" cy="17.5" r="1" fill="#FFFFFF"/><path d="M17.5,7.5 L21,11 L17.5,14.5 L14,11 Z" fill="#FF8C00" stroke="#8B0000" stroke-width="0.4"/><path d="M27.5,17.5 L24,21 L20.5,17.5 L24,14 Z" fill="#FF8C00" stroke="#8B0000" stroke-width="0.4"/><path d="M17.5,27.5 L14,24 L17.5,20.5 L21,24 Z" fill="#FF8C00" stroke="#8B0000" stroke-width="0.4"/><path d="M7.5,17.5 L11,14 L14.5,17.5 L11,21 Z" fill="#FF8C00" stroke="#8B0000" stroke-width="0.4"/><path d="M23,9 L26,12 L23,15 L20,12 Z" fill="#FFA500" stroke="#8B0000" stroke-width="0.4"/><path d="M26,26 L23,29 L20,26 L23,23 Z" fill="#FFA500" stroke="#8B0000" stroke-width="0.4"/><path d="M12,29 L9,26 L12,23 L15,26 Z" fill="#FFA500" stroke="#8B0000" stroke-width="0.4"/><path d="M9,12 L12,9 L15,12 L12,15 Z" fill="#FFA500" stroke="#8B0000" stroke-width="0.4"/><circle cx="8" cy="8" r="2.5" fill="#FFB6C1" stroke="#8B0000" stroke-width="0.4"/><circle cx="27" cy="8" r="2.5" fill="#FFB6C1" stroke="#8B0000" stroke-width="0.4"/><circle cx="27" cy="27" r="2.5" fill="#FFB6C1" stroke="#8B0000" stroke-width="0.4"/><circle cx="8" cy="27" r="2.5" fill="#FFB6C1" stroke="#8B0000" stroke-width="0.4"/><path d="M8,5 Q8,2 11,2" fill="none" stroke="#228B22" stroke-width="0.5"/><path d="M27,5 Q27,2 24,2" fill="none" stroke="#228B22" stroke-width="0.5"/><path d="M8,30 Q8,33 11,33" fill="none" stroke="#228B22" stroke-width="0.5"/><path d="M27,30 Q27,33 24,33" fill="none" stroke="#228B22" stroke-width="0.5"/><polygon points="17.5,16 18,17.5 17.5,19 17,17.5" fill="#FFFFFF"/></pattern></defs><rect width="100%" height="100%" fill="url(#bunga)"/></svg>',
                    'complexity_score' => 9
                ],
                'base_color' => '#FF6347',
                'color_variations' => ['#FF6347,#8B0000,#FFD700,#FF8C00,#FFA500,#FFB6C1', '#FF69B4,#4B0082,#FFD700,#FFFFFF', '#FF8C00,#228B22,#FFD700,#8B0000'],
                'base_price_multiplier' => 1.75,
                'popularity_score' => 22,
            ],
            [
                'name' => 'Pinalantikan',
                'description' => 'Sacred nested diamond pattern representing the bamboo fish traps (bubo) and protective amulets (anting-anting) of Yakan warriors. Features five concentric diamonds with intricate geometric borders, each containing smaller diamond motifs representing generations of protection. The pattern includes traditional lightning bolts and wave symbols representing the Sulu Sea and Basilan mountains.',
                'category' => 'traditional',
                'difficulty_level' => 'expert',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="pinalantikan" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse"><polygon points="25,8 38,25 25,42 12,25" fill="#4B0082" stroke="#FFD700" stroke-width="2.5"/><polygon points="25,12 35,25 25,38 15,25" fill="#8B0000" stroke="#FFD700" stroke-width="2"/><polygon points="25,15 32,25 25,35 18,25" fill="#FFD700" stroke="#8B0000" stroke-width="1.5"/><polygon points="25,18 28,25 25,32 22,25" fill="#FFFFFF" stroke="#4B0082" stroke-width="1"/><polygon points="25,20 26.5,25 25,30 23.5,25" fill="#4B0082" stroke="#FFD700" stroke-width="0.5"/><polygon points="25,22 25.5,25 25,28 24.5,25" fill="#FFFFFF" stroke="#8B0000" stroke-width="0.3"/><line x1="12" y1="25" x2="38" y2="25" stroke="#FFFFFF" stroke-width="0.8" opacity="0.6"/><line x1="25" y1="8" x2="25" y2="42" stroke="#FFFFFF" stroke-width="0.8" opacity="0.6"/><path d="M15,15 L20,20" stroke="#FFD700" stroke-width="0.5" opacity="0.7"/><path d="M30,20 L35,15" stroke="#FFD700" stroke-width="0.5" opacity="0.7"/><path d="M30,30 L35,35" stroke="#FFD700" stroke-width="0.5" opacity="0.7"/><path d="M15,35 L20,30" stroke="#FFD700" stroke-width="0.5" opacity="0.7"/><circle cx="25" cy="25" r="0.8" fill="#FFFFFF"/><circle cx="20" cy="20" r="0.5" fill="#FFD700"/><circle cx="30" cy="20" r="0.5" fill="#FFD700"/><circle cx="30" cy="30" r="0.5" fill="#FFD700"/><circle cx="20" cy="30" r="0.5" fill="#FFD700"/></pattern></defs><rect width="100%" height="100%" fill="url(#pinalantikan)"/></svg>',
                    'complexity_score' => 8
                ],
                'base_color' => '#4B0082',
                'color_variations' => ['#4B0082,#FFD700,#8B0000,#FFD700,#FFFFFF', '#8B0000,#FFD700,#4B0082,#FFFFFF', '#228B22,#FFD700,#4B0082,#FFFFFF'],
                'base_price_multiplier' => 1.60,
                'popularity_score' => 18,
            ],
            [
                'name' => 'Suhul',
                'description' => 'Sacred ocean wave pattern representing the Sulu Sea\'s eternal rhythms and the monsoon winds (habagat) that guide Yakan sailors. Features multiple layered waves with different intensities, representing the changing tides and seasons. The waves contain starfish motifs and navigation constellations used by Yakan fishermen for safe passage. Each wave crest includes traditional diamond patterns representing sea foam and marine life.',
                'category' => 'traditional',
                'difficulty_level' => 'advanced',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="suhul" x="0" y="0" width="60" height="30" patternUnits="userSpaceOnUse"><path d="M0,15 Q15,0 30,15 T60,15" stroke="#1E90FF" stroke-width="3" fill="none"/><path d="M0,20 Q15,5 30,20 T60,20" stroke="#4682B4" stroke-width="2.5" fill="none"/><path d="M0,10 Q15,-5 30,10 T60,10" stroke="#87CEEB" stroke-width="2" fill="none"/><path d="M0,25 Q15,10 30,25 T60,25" stroke="#00CED1" stroke-width="1.5" fill="none"/><path d="M0,5 Q15,-10 30,5 T60,5" stroke="#B0E0E6" stroke-width="1" fill="none"/><polygon points="15,8 17,10 15,12 13,10" fill="#FFD700" stroke="#FFA500" stroke-width="0.5"/><polygon points="45,8 47,10 45,12 43,10" fill="#FFD700" stroke="#FFA500" stroke-width="0.5"/><polygon points="30,3 31,5 30,7 29,5" fill="#FFFFFF" stroke="#87CEEB" stroke-width="0.3"/><polygon points="15,18 17,20 15,22 13,20" fill="#FFA500" stroke="#FFD700" stroke-width="0.4"/><polygon points="45,18 47,20 45,22 43,20" fill="#FFA500" stroke="#FFD700" stroke-width="0.4"/><circle cx="10" cy="15" r="1.5" fill="#FFD700" opacity="0.8"/><circle cx="50" cy="15" r="1.5" fill="#FFD700" opacity="0.8"/><circle cx="30" cy="10" r="1" fill="#FFFFFF" opacity="0.7"/><circle cx="30" cy="20" r="1" fill="#FFFFFF" opacity="0.7"/><path d="M5,12 L8,15" stroke="#FFD700" stroke-width="0.5" opacity="0.6"/><path d="M55,12 L52,15" stroke="#FFD700" stroke-width="0.5" opacity="0.6"/></pattern></defs><rect width="100%" height="100%" fill="url(#suhul)"/></svg>',
                    'complexity_score' => 7
                ],
                'base_color' => '#4682B4',
                'color_variations' => ['#4682B4,#00CED1,#87CEEB,#1E90FF,#FFD700,#FFA500,#FFFFFF', '#4682B4,#FFFFFF,#8B0000,#FFD700', '#4B0082,#FFD700,#87CEEB,#FFFFFF'],
                'base_price_multiplier' => 1.40,
                'popularity_score' => 15,
            ],
            [
                'name' => 'Kabkaban',
                'description' => 'Traditional interlocking squares pattern inspired by the ancient Yakan communal houses (baul) and the sacred rice terraces of Basilan. Features complex geometric arrangements with multiple square sizes representing different family units. Each square contains inner patterns symbolizing the hearth and home. The interlocking design represents how Yakan families connect to form the larger community while maintaining their individual identities.',
                'category' => 'traditional',
                'difficulty_level' => 'intermediate',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="kabkaban" x="0" y="0" width="28" height="28" patternUnits="userSpaceOnUse"><rect x="2" y="2" width="8" height="8" fill="#8B4513" stroke="#D2691E" stroke-width="1.5"/><rect x="10" y="10" width="8" height="8" fill="#D2691E" stroke="#8B4513" stroke-width="1.5"/><rect x="10" y="2" width="8" height="8" fill="#DEB887" stroke="#8B4513" stroke-width="1.5"/><rect x="2" y="10" width="8" height="8" fill="#DEB887" stroke="#D2691E" stroke-width="1.5"/><rect x="18" y="18" width="8" height="8" fill="#8B4513" stroke="#D2691E" stroke-width="1.5"/><rect x="18" y="10" width="8" height="8" fill="#D2691E" stroke="#8B4513" stroke-width="1.5"/><rect x="10" y="18" width="8" height="8" fill="#DEB887" stroke="#8B4513" stroke-width="1.5"/><rect x="2" y="18" width="8" height="8" fill="#DEB887" stroke="#D2691E" stroke-width="1.5"/><rect x="18" y="2" width="8" height="8" fill="#8B4513" stroke="#D2691E" stroke-width="1.5"/><rect x="4" y="4" width="4" height="4" fill="#F4A460" stroke="#8B4513" stroke-width="0.5"/><rect x="12" y="4" width="4" height="4" fill="#F4A460" stroke="#D2691E" stroke-width="0.5"/><rect x="12" y="12" width="4" height="4" fill="#F4A460" stroke="#8B4513" stroke-width="0.5"/><rect x="4" y="12" width="4" height="4" fill="#F4A460" stroke="#D2691E" stroke-width="0.5"/><rect x="20" y="20" width="4" height="4" fill="#F4A460" stroke="#8B4513" stroke-width="0.5"/><rect x="20" y="12" width="4" height="4" fill="#F4A460" stroke="#8B4513" stroke-width="0.5"/><rect x="12" y="20" width="4" height="4" fill="#F4A460" stroke="#D2691E" stroke-width="0.5"/><rect x="4" y="20" width="4" height="4" fill="#F4A460" stroke="#D2691E" stroke-width="0.5"/><rect x="20" y="4" width="4" height="4" fill="#F4A460" stroke="#8B4513" stroke-width="0.5"/><line x1="0" y1="14" x2="28" y2="14" stroke="#FFD700" stroke-width="0.8" opacity="0.4"/><line x1="14" y1="0" x2="14" y2="28" stroke="#FFD700" stroke-width="0.8" opacity="0.4"/></pattern></defs><rect width="100%" height="100%" fill="url(#kabkaban)"/></svg>',
                    'complexity_score' => 6
                ],
                'base_color' => '#8B4513',
                'color_variations' => ['#8B4513,#D2691E,#DEB887,#FFD700', '#8B0000,#FFD700,#4B0082,#FFFFFF', '#654321,#8B4513,#D2691E,#F4A460'],
                'base_price_multiplier' => 1.20,
                'popularity_score' => 12,
            ],
            [
                'name' => 'Laggi',
                'description' => 'Sacred eight-pointed star pattern representing the constellations that guide Yakan fishermen and farmers through the Sulu Sea and Basilan mountains. Each star contains intricate geometric patterns with smaller stars representing ancestral spirits. The eight points symbolize the sacred directions in Yakan cosmology. Used in ceremonial clothing for weddings, harvest festivals, and important community rituals.',
                'category' => 'traditional',
                'difficulty_level' => 'advanced',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="laggi" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse"><polygon points="20,4 23,16 35,16 25,24 28,36 20,28 12,36 15,24 5,16 17,16" fill="#FFD700" stroke="#8B0000" stroke-width="1.8"/><polygon points="20,8 22,16 30,16 24,21 26,29 20,23 14,29 16,21 10,16 18,16" fill="#FF8C00" stroke="#8B0000" stroke-width="1.2"/><polygon points="20,12 21,16 25,16 22,19 23,25 20,20 17,25 18,19 15,16 19,16" fill="#FFA500" stroke="#8B0000" stroke-width="0.8"/><polygon points="20,16 20.5,18 22,18 20.5,19.5 20,21 19.5,19.5 18,18 19.5,18" fill="#FFFFFF" stroke="#FFD700" stroke-width="0.5"/><circle cx="20" cy="20" r="2.5" fill="#FFD700" stroke="#8B0000" stroke-width="1"/><circle cx="20" cy="20" r="1" fill="#FFFFFF" stroke="#8B0000" stroke-width="0.5"/><circle cx="20" cy="20" r="0.5" fill="#8B0000"/><polygon points="20,32 21,35 23,32" fill="#4B0082" stroke="#FFD700" stroke-width="0.8"/><line x1="20" y1="20" x2="20" y2="32" stroke="#4B0082" stroke-width="0.4"/><polygon points="8,8 9,9 8,10 7,9" fill="#FFD700" stroke="#8B0000" stroke-width="0.3"/><polygon points="32,8 33,9 32,10 31,9" fill="#FFD700" stroke="#8B0000" stroke-width="0.3"/><polygon points="8,32 9,33 8,34 7,33" fill="#FFD700" stroke="#8B0000" stroke-width="0.3"/><polygon points="32,32 33,33 32,34 31,33" fill="#FFD700" stroke="#8B0000" stroke-width="0.3"/></pattern></defs><rect width="100%" height="100%" fill="url(#laggi)"/></svg>',
                    'complexity_score' => 8
                ],
                'base_color' => '#FFD700',
                'color_variations' => ['#FFD700,#8B0000,#FF8C00,#FFA500,#FFFFFF,#4B0082', '#FFD700,#4B0082,#FFFFFF,#228B22', '#FFD700,#228B22,#8B0000,#FF8C00'],
                'base_price_multiplier' => 1.50,
                'popularity_score' => 20,
            ],
            [
                'name' => 'Bennig',
                'description' => 'Master-level sacred spiral pattern representing the eternal flow of life, seasons, and agricultural cycles in Yakan culture. Inspired by the nautilus shells found in Sulu Sea waters and the circular pangalay dances performed during festivals. Features triple interconnected spirals with intricate geometric patterns representing past, present, and future. Each spiral contains smaller motifs representing the continuity of Yakan traditions through generations.',
                'category' => 'traditional',
                'difficulty_level' => 'master',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="bennig" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M25,25 Q32,20 25,15 Q18,20 25,25 Q32,30 25,35 Q18,30 25,25" stroke="#228B22" stroke-width="2.5" fill="none"/><path d="M25,25 Q35,22 35,15 Q25,18 25,25 Q35,35 35,28 Q25,32 25,25" stroke="#8B0000" stroke-width="2" fill="none" opacity="0.8"/><path d="M25,25 Q40,25 40,40 Q25,25 10,25 Q25,25 25,10" stroke="#4B0082" stroke-width="1.5" fill="none" opacity="0.7"/><path d="M25,25 Q30,22 25,17 Q20,22 25,25" stroke="#FFD700" stroke-width="1.8" fill="none"/><path d="M25,25 Q28,23 25,20 Q22,23 25,25" stroke="#FFA500" stroke-width="1.2" fill="none" opacity="0.8"/><circle cx="25" cy="25" r="3.5" fill="#FFD700" stroke="#8B0000" stroke-width="1.5"/><circle cx="25" cy="25" r="2" fill="#FFFFFF" stroke="#228B22" stroke-width="1"/><circle cx="25" cy="25" r="1" fill="#8B0000"/><circle cx="25" cy="25" r="0.5" fill="#FFFFFF"/><path d="M22,22 Q25,19 28,22 Q25,25 22,22" stroke="#FFA500" stroke-width="0.4" fill="none" opacity="0.7"/><path d="M15,15 Q18,12 21,15 Q18,18 15,15" stroke="#4B0082" stroke-width="0.3" fill="none" opacity="0.6"/><path d="M29,29 Q32,26 35,29 Q32,32 29,29" stroke="#4B0082" stroke-width="0.3" fill="none" opacity="0.6"/><path d="M15,35 Q18,32 21,35 Q18,38 15,35" stroke="#228B22" stroke-width="0.3" fill="none" opacity="0.6"/><path d="M35,15 Q32,12 29,15 Q32,18 35,15" stroke="#228B22" stroke-width="0.3" fill="none" opacity="0.6"/></pattern></defs><rect width="100%" height="100%" fill="url(#bennig)"/></svg>',
                    'complexity_score' => 9
                ],
                'base_color' => '#228B22',
                'color_variations' => ['#228B22,#8B0000,#FFD700,#4B0082,#FFA500,#FFFFFF', '#228B22,#4B0082,#FFD700,#8B0000', '#228B22,#FFFFFF,#8B0000,#FFD700'],
                'base_price_multiplier' => 1.80,
                'popularity_score' => 16,
            ],
            [
                'name' => 'Pangapun',
                'description' => 'Sacred triangle pattern symbolizing the three sacred mountains of Basilan and the three pillars of Yakan society: family (kaluluwa), community (bayan), and tradition (adat). The triangles also represent the traditional Yakan spear tips (bangkaw) and the protective mountains that surround their homeland. Each triangle contains the sacred mountain spirit.',
                'category' => 'traditional',
                'difficulty_level' => 'advanced',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="pangapun" x="0" y="0" width="36" height="36" patternUnits="userSpaceOnUse"><polygon points="18,6 30,30 6,30" fill="#FF8C00" stroke="#8B0000" stroke-width="3"/><polygon points="18,12 27,27 9,27" fill="#FFD700" stroke="#8B0000" stroke-width="2"/><polygon points="18,18 24,24 12,24" fill="#FFFFFF" stroke="#FF8C00" stroke-width="1"/><polygon points="18,21 21,24 15,24" fill="#4B0082" stroke="#FFD700" stroke-width="0.5"/><line x1="18" y1="18" x2="18" y2="30" stroke="#8B0000" stroke-width="2"/><line x1="18" y1="30" x2="12" y2="24" stroke="#8B0000" stroke-width="1"/><line x1="18" y1="30" x2="24" y2="24" stroke="#8B0000" stroke-width="1"/><circle cx="18" cy="18" r="1" fill="#FFD700"/><polygon points="18,3 20,8 16,8" fill="#FF8C00"/></pattern></defs><rect width="100%" height="100%" fill="url(#pangapun)"/></svg>',
                    'complexity_score' => 7
                ],
                'base_color' => '#FF8C00',
                'color_variations' => ['#FF8C00,#8B0000,#FFD700,#FFFFFF,#4B0082', '#FF8C00,#4B0082,#FFFFFF,#228B22', '#FF8C00,#228B22,#8B0000,#FFD700'],
                'base_price_multiplier' => 1.45,
                'popularity_score' => 14,
            ],
            [
                'name' => 'Sarang Kayu',
                'description' => 'Sacred honeycomb pattern celebrating the traditional honey gathering and the sacred bees in Yakan ecology. Each hexagon represents a family unit working together for community prosperity. The pattern is woven during harvest festivals and represents the sweet rewards of cooperation and sustainable living in harmony with nature.',
                'category' => 'traditional',
                'difficulty_level' => 'expert',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="sarang" x="0" y="0" width="40" height="35" patternUnits="userSpaceOnUse"><polygon points="20,0 30,6 30,14 20,20 10,14 10,6" fill="#FFB347" stroke="#8B0000" stroke-width="2"/><polygon points="0,17 10,23 10,31 0,37 -10,31 -10,23" fill="#FFA500" stroke="#8B0000" stroke-width="2"/><polygon points="40,17 50,23 50,31 40,37 30,31 30,23" fill="#FF8C00" stroke="#8B0000" stroke-width="2"/><polygon points="20,15 27,20 27,27 20,32 13,27 13,20" fill="#FFD700" stroke="#8B0000" stroke-width="1"/><polygon points="20,25 27,30 27,37 20,42 13,37 13,30" fill="#FFD700" stroke="#8B0000" stroke-width="1"/><polygon points="20,5 25,10 25,15 20,20 15,15 15,10" fill="#FFA500" stroke="#8B0000" stroke-width="0.5"/><polygon points="20,30 25,35 25,40 20,45 15,40 15,35" fill="#FFA500" stroke="#8B0000" stroke-width="0.5"/><circle cx="20" cy="20" r="2" fill="#FFFFFF" stroke="#8B0000" stroke-width="1"/><circle cx="20" cy="20" r="1" fill="#8B0000"/></pattern></defs><rect width="100%" height="100%" fill="url(#sarang)"/></svg>',
                    'complexity_score' => 8
                ],
                'base_color' => '#FFB347',
                'color_variations' => ['#FFB347,#8B0000,#FFA500,#FF8C00,#FFD700,#FFFFFF', '#FFB347,#4B0082,#FFD700,#FFFFFF', '#FFB347,#228B22,#FF8C00,#8B0000'],
                'base_price_multiplier' => 1.55,
                'popularity_score' => 17,
            ],
            [
                'name' => 'Ikan Mas',
                'description' => 'Sacred fish pattern celebrating the abundant marine life of Basilan waters and the fishing heritage. The fish represents prosperity, good fortune, and the bounty of the Sulu Sea. Multiple fish swimming in formation symbolize community cooperation and the interconnected nature of Yakan fishing families who work together for the common good.',
                'category' => 'traditional',
                'difficulty_level' => 'advanced',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="ikan" x="0" y="0" width="50" height="40" patternUnits="userSpaceOnUse"><ellipse cx="25" cy="20" rx="16" ry="8" fill="#4682B4" stroke="#00CED1" stroke-width="2"/><polygon points="9,20 3,16 3,24" fill="#00CED1" stroke="#4682B4" stroke-width="1"/><circle cx="31" cy="17" r="2" fill="#FFFFFF" stroke="#00CED1" stroke-width="0.5"/><circle cx="31" cy="23" r="2" fill="#FFFFFF" stroke="#00CED1" stroke-width="0.5"/><path d="M41,20 Q46,16 51,20 Q46,24 41,20" stroke="#4682B4" stroke-width="2" fill="none"/><ellipse cx="25" cy="10" rx="12" ry="6" fill="#87CEEB" stroke="#4682B4" stroke-width="1" opacity="0.7"/><ellipse cx="25" cy="30" rx="12" ry="6" fill="#87CEEB" stroke="#4682B4" stroke-width="1" opacity="0.7"/><ellipse cx="10" cy="8" rx="8" ry="4" fill="#1E90FF" stroke="#4682B4" stroke-width="0.5" opacity="0.5"/><ellipse cx="40" cy="32" rx="8" ry="4" fill="#1E90FF" stroke="#4682B4" stroke-width="0.5" opacity="0.5"/><circle cx="25" cy="20" r="1" fill="#FFD700"/></pattern></defs><rect width="100%" height="100%" fill="url(#ikan)"/></svg>',
                    'complexity_score' => 7
                ],
                'base_color' => '#4682B4',
                'color_variations' => ['#4682B4,#00CED1,#87CEEB,#1E90FF,#FFD700,#FFFFFF', '#4682B4,#FFD700,#8B0000,#FF8C00', '#4682B4,#4B0082,#FFFFFF,#87CEEB'],
                'base_price_multiplier' => 1.35,
                'popularity_score' => 19,
            ],
            [
                'name' => 'Kalasag',
                'description' => 'Sacred shield pattern inspired by the traditional Yakan warrior shields (kalasag) used for protection during conflicts. The concentric diamond shapes represent layers of spiritual and physical protection - from the individual to the community to the ancestors. This pattern symbolizes courage, strength, and the warrior spirit that protects Yakan communities.',
                'category' => 'traditional',
                'difficulty_level' => 'master',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="kalasag" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse"><polygon points="25,5 40,25 25,45 10,25" fill="#8B0000" stroke="#FFD700" stroke-width="3"/><polygon points="25,8 37,25 25,42 13,25" fill="#4B0082" stroke="#FFD700" stroke-width="2"/><polygon points="25,11 34,25 25,39 16,25" fill="#228B22" stroke="#FFD700" stroke-width="2"/><polygon points="25,14 31,25 25,36 19,25" fill="#FFD700" stroke="#8B0000" stroke-width="1"/><polygon points="25,17 28,25 25,33 22,25" fill="#FFFFFF" stroke="#4B0082" stroke-width="1"/><polygon points="25,20 26.5,25 25,30 23.5,25" fill="#8B0000" stroke="#FFD700" stroke-width="0.5"/><line x1="10" y1="25" x2="40" y2="25" stroke="#FFD700" stroke-width="1" opacity="0.5"/><line x1="25" y1="5" x2="25" y2="45" stroke="#FFD700" stroke-width="1" opacity="0.5"/><circle cx="25" cy="25" r="1" fill="#FFFFFF"/></pattern></defs><rect width="100%" height="100%" fill="url(#kalasag)"/></svg>',
                    'complexity_score' => 10
                ],
                'base_color' => '#8B0000',
                'color_variations' => ['#8B0000,#FFD700,#4B0082,#228B22,#FFD700,#FFFFFF', '#8B0000,#FFD700,#FFFFFF,#000000', '#8B0000,#4B0082,#FFD700,#228B22'],
                'base_price_multiplier' => 1.90,
                'popularity_score' => 23,
            ],
            [
                'name' => 'Tali',
                'description' => 'Sacred interwoven rope pattern representing the strong bonds that tie Yakan families and communities together. The interlocking design symbolizes unity, cooperation, and the interconnected nature of Yakan social structure. Each knot represents a promise or commitment within the community, and the continuous rope represents the eternal flow of Yakan culture.',
                'category' => 'traditional',
                'difficulty_level' => 'expert',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="tali" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M5,20 Q20,5 35,20 Q20,35 5,20" stroke="#8B4513" stroke-width="4" fill="none"/><path d="M5,20 Q20,35 35,20" stroke="#D2691E" stroke-width="3" fill="none"/><path d="M20,5 Q35,20 20,35 Q5,20 20,5" stroke="#DEB887" stroke-width="3" fill="none"/><path d="M10,20 Q20,10 30,20 Q20,30 10,20" stroke="#8B4513" stroke-width="2" fill="none"/><path d="M20,10 Q30,20 20,30 Q10,20 20,10" stroke="#D2691E" stroke-width="2" fill="none"/><circle cx="20" cy="20" r="4" fill="#FFD700" stroke="#8B0000" stroke-width="2"/><circle cx="20" cy="20" r="2" fill="#FFFFFF" stroke="#8B4513" stroke-width="1"/><circle cx="20" cy="20" r="1" fill="#8B0000"/><circle cx="5" cy="20" r="2" fill="#FFFFFF" stroke="#8B4513" stroke-width="1"/><circle cx="35" cy="20" r="2" fill="#FFFFFF" stroke="#8B4513" stroke-width="1"/><circle cx="20" cy="5" r="2" fill="#FFFFFF" stroke="#D2691E" stroke-width="1"/><circle cx="20" cy="35" r="2" fill="#FFFFFF" stroke="#D2691E" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#tali)"/></svg>',
                    'complexity_score' => 8
                ],
                'base_color' => '#8B4513',
                'color_variations' => ['#8B4513,#D2691E,#DEB887,#FFD700,#FFFFFF', '#8B0000,#FFD700,#4B0082,#FFFFFF', '#654321,#8B4513,#D2691E,#F4A460'],
                'base_price_multiplier' => 1.65,
                'popularity_score' => 13,
            ],
            [
                'name' => 'Langgal',
                'description' => 'Sacred mosque pattern representing the Islamic faith central to Yakan culture. The geometric designs symbolize the architectural beauty of traditional Yakan mosques and the spiritual connection to Allah. The interlocking patterns represent the unity of the Muslim community (ummah) and the five pillars of Islam.',
                'category' => 'traditional',
                'difficulty_level' => 'master',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="langgal" x="0" y="0" width="45" height="45" patternUnits="userSpaceOnUse"><rect x="5" y="5" width="35" height="35" fill="#4B0082" stroke="#FFD700" stroke-width="2"/><rect x="10" y="10" width="25" height="25" fill="#FFFFFF" stroke="#4B0082" stroke-width="1"/><rect x="15" y="15" width="15" height="15" fill="#4B0082" stroke="#FFD700" stroke-width="1"/><polygon points="22.5,18 25,22 22.5,26 20,22" fill="#FFD700" stroke="#8B0000" stroke-width="1"/><polygon points="22.5,20 24,22 22.5,24 21,22" fill="#8B0000"/><rect x="18" y="20" width="9" height="2" fill="#FFD700"/><rect x="20" y="18" width="5" height="6" fill="#4B0082" stroke="#FFD700" stroke-width="0.5"/><circle cx="22.5" cy="22.5" r="8" fill="none" stroke="#FFD700" stroke-width="0.5"/><circle cx="22.5" cy="22.5" r="12" fill="none" stroke="#8B0000" stroke-width="0.5"/><circle cx="22.5" cy="22.5" r="16" fill="none" stroke="#4B0082" stroke-width="0.5"/></pattern></defs><rect width="100%" height="100%" fill="url(#langgal)"/></svg>',
                    'complexity_score' => 9
                ],
                'base_color' => '#4B0082',
                'color_variations' => ['#4B0082,#FFD700,#FFFFFF,#8B0000', '#4B0082,#FFD700,#228B22,#FFFFFF', '#4B0082,#FFFFFF,#FFD700,#8B0000'],
                'base_price_multiplier' => 1.85,
                'popularity_score' => 21,
            ],
            [
                'name' => 'Saput Tangan',
                'description' => 'Traditional handkerchief pattern featuring the intricate geometric designs woven for marriage dowries and special gifts. The pattern combines multiple sacred symbols - stars, diamonds, and flowers - representing the different aspects of Yakan life coming together in harmony. Each element has specific meaning in courtship and marriage traditions.',
                'category' => 'traditional',
                'difficulty_level' => 'master',
                'pattern_data' => [
                    'svg' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="saput" x="0" y="0" width="55" height="55" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="55" height="55" fill="#FFD700" stroke="#8B0000" stroke-width="2"/><rect x="5" y="5" width="45" height="45" fill="#FFFFFF" stroke="#4B0082" stroke-width="1"/><rect x="10" y="10" width="35" height="35" fill="#8B0000" stroke="#FFD700" stroke-width="1"/><rect x="15" y="15" width="25" height="25" fill="#4B0082" stroke="#FFFFFF" stroke-width="1"/><polygon points="27.5,20 30,25 27.5,30 25,25" fill="#FFD700" stroke="#8B0000" stroke-width="1"/><polygon points="27.5,22 29,25 27.5,28 26,25" fill="#FFFFFF"/><circle cx="27.5" cy="25" r="8" fill="none" stroke="#FFD700" stroke-width="0.5"/><circle cx="15" cy="15" r="3" fill="#FFD700" stroke="#8B0000" stroke-width="0.5"/><circle cx="40" cy="15" r="3" fill="#FFD700" stroke="#8B0000" stroke-width="0.5"/><circle cx="15" cy="40" r="3" fill="#FFD700" stroke="#8B0000" stroke-width="0.5"/><circle cx="40" cy="40" r="3" fill="#FFD700" stroke="#8B0000" stroke-width="0.5"/><line x1="10" y1="27.5" x2="45" y2="27.5" stroke="#FFD700" stroke-width="0.5"/><line x1="27.5" y1="10" x2="27.5" y2="45" stroke="#FFD700" stroke-width="0.5"/></pattern></defs><rect width="100%" height="100%" fill="url(#saput)"/></svg>',
                    'complexity_score' => 10
                ],
                'base_color' => '#FFD700',
                'color_variations' => ['#FFD700,#8B0000,#FFFFFF,#4B0082', '#FFD700,#4B0082,#8B0000,#FFFFFF', '#FFD700,#8B0000,#228B22,#FFFFFF'],
                'base_price_multiplier' => 1.95,
                'popularity_score' => 24,
            ],
        ];

        foreach ($patterns as $pattern) {
            // Set is_active to true for all patterns
            $pattern['is_active'] = true;
            YakanPattern::create($pattern);
        }
    }
}
