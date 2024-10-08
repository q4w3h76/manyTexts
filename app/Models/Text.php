<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'text',
        'tags',
        'user_id',
        'is_public',
        'expiration',
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = self::generateUniqueSlug();
        });
    }

    private static function generateUniqueSlug(int $length = 10)
    {
        do {
            $slug = Str::random(10);
        } while (self::whereSlug($slug)->exists());
        return $slug;
    }
}
