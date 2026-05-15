<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coffees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('origin');
            $table->enum('roast_level', ['light', 'medium', 'dark']);
            $table->json('flavor_notes')->nullable();
            $table->decimal('price_eur', 8, 2);
            $table->string('image_url')->nullable();
            $table->text('description');
            $table->boolean('featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coffees');
    }
};
