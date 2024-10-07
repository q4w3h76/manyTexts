<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'tags',
        'user_id',
        'is_public',
        'expiration',
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];
}
