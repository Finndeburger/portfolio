<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Site::create([
            'title' => 'About',
            'slug' => 'about',
            'dummy_url' => 'https://finnharmens.com/about',
            'description' => 'About Finn Harmens',
            'tags' => ['portfolio', 'about', 'about me', 'finn', 'info'],
            'sponsored' => false,
            'database_connection' => null,
            'database_table' => null,
            'database_meta' => null,
        ]);

        Site::create([
            'title' => 'Test Site',
            'slug' => 'test',
            'dummy_url' => 'https://finnharmens.dev/test',
            'description' => 'Test page for routing',
            'tags' => ['test', 'development', 'router', 'placeholder'],
            'sponsored' => false,
            'database_connection' => null,
            'database_table' => null,
            'database_meta' => null,
        ]);

        Site::create([
            'title' => 'BergCoffee',
            'slug' => 'bergcoffee',
            'dummy_url' => 'https://bergcoffee.com',
            'description' => 'BergCoffee: real coffee, grounded and down-to-earth.',
            'tags' => ['bergcoffee', 'coffee', 'drinks', 'store', 'shop', 'e-commerce', 'products'],
            'sponsored' => false,
            'database_connection' => null,
            'database_table' => null,
            'database_meta' => null,
        ]);

        Site::create([
            'title' => 'Authentication',
            'slug' => 'authentication',
            'dummy_url' => 'https://example.com/authentication',
            'description' => 'A description for search results',
            'tags' => ['tag1', 'tag2', 'tag3'],
            'sponsored' => false,
            'database_connection' => null,
            'database_table' => null,
            'database_meta' => null,
        ]);
    }
}
