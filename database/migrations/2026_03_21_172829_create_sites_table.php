<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id(); // Auto-incrementing integer primary key
            $table->string('title'); // "About", "Test", etc.
            $table->string('slug')->unique(); // "about", "test" — used in URLs, must be unique
            $table->string('dummy_url'); // Fake URL shown in the browser bar: "https://finnharmens.com/about"
            $table->text('description'); // Search result snippet text
            $table->json('tags'); // JSON array of search keywords: ["portfolio", "about", "finn"]
            $table->boolean('sponsored')->default(false); // Whether this site can appear as a "Sponsored" result
            $table->timestamps(); // created_at and updated_at columns (auto-managed by Laravel)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
