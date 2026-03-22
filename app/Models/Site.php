<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'dummy_url',
        'description',
        'tags',
        'sponsored',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'sponsored' => 'boolean',
        ];
    }
}
