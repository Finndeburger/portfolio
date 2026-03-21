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
        ]);

        Site::create([
            'title' => 'Test Site',
            'slug' => 'test',
            'dummy_url' => 'https://finnharmens.dev/test',
            'description' => 'Test page for routing',
            'tags' => ['test', 'development', 'router', 'placeholder'],
            'sponsored' => false,
        ]);
    }
}
