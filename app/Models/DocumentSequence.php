<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSequence extends Model
{
    protected $fillable = [
        'type',
        'prefix',
        'next_number',
        'padding',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'next_number' => 'integer',
            'padding' => 'integer',
        ];
    }
}
