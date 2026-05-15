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
        'database_connection',
        'database_table',
        'database_meta',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'sponsored' => 'boolean',
            'database_meta' => 'array',
        ];
    }
}
