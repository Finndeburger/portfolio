<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected function casts(): array {
        return [
            'tags' => 'array', 
            'sponsored' => 'boolean',
        ];
    }
}
