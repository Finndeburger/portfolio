<?php

namespace Database\Seeders;

use App\Models\Coffee;
use Illuminate\Database\Seeder;

class CoffeeSeeder extends Seeder
{
    public function run(): void
    {
        $coffees = [
            [
                'name'         => 'Berg Signature Blend',
                'origin'       => 'Colombia / Ethiopia',
                'roast_level'  => 'medium',
                'flavor_notes' => ['chocolate', 'caramel', 'citrus'],
                'price_eur'    => 12.50,
                'description'  => 'Our flagship blend — smooth, balanced, and endlessly drinkable.',
                'featured'     => true,
            ],
            [
                'name'         => 'Ethiopian Sunrise',
                'origin'       => 'Ethiopia',
                'roast_level'  => 'light',
                'flavor_notes' => ['blueberry', 'jasmine', 'honey'],
                'price_eur'    => 14.00,
                'description'  => 'A bright, fruity single-origin with floral aromatics.',
                'featured'     => false,
            ],
            [
                'name'         => 'Colombian Summit',
                'origin'       => 'Colombia',
                'roast_level'  => 'medium',
                'flavor_notes' => ['nutty', 'brown sugar', 'apple'],
                'price_eur'    => 11.75,
                'description'  => 'Classic Colombian profile — clean cup, sweet finish.',
                'featured'     => false,
            ],
            [
                'name'         => 'Dark Roast Reserve',
                'origin'       => 'Brazil / Indonesia',
                'roast_level'  => 'dark',
                'flavor_notes' => ['smoky', 'dark chocolate', 'walnut'],
                'price_eur'    => 13.00,
                'description'  => 'Bold and full-bodied for those who like it strong.',
                'featured'     => true,
            ],
        ];

        foreach ($coffees as $coffee) {
            Coffee::create($coffee);
        }
    }
}
