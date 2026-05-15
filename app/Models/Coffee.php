<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coffee extends Model
{
    protected $fillable = [
        'name', 'origin', 'roast_level', 'flavor_notes',
        'price_eur', 'image_url', 'description', 'featured',
    ];

    protected function casts(): array
    {
        return [
            'flavor_notes' => 'array',
            'price_eur'    => 'decimal:2',
            'featured'     => 'boolean',
        ];
    }
}
