<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Text extends Model
{
    use HasFactory, Filterable;

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

    public function scopePublic(Builder $query) {
        return $query->whereIsPublic(true);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = self::generateUniqueSlug();
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    private static function generateUniqueSlug(int $length = 10)
    {
        do {
            $slug = Str::random(10);
        } while (self::whereSlug($slug)->exists());
        return $slug;
    }
}
