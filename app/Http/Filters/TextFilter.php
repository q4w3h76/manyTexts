<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class TextFilter extends AbstractFilter
{

    public const TITLE = 'title';
    public const TAGS = 'tags';
    public const USER_ID = 'user_id';

    protected function getCallbacks(): array
    {
        return [
            self::TITLE => [$this, 'title'],
            self::TAGS => [$this, 'tags'],
            self::USER_ID => [$this, 'userId'],
        ];
    }

    public function title(Builder $builder, string $title): void
    {
        $builder->where('title', 'LIKE', "%{$title}%");
    }

    public function tags(Builder $builder, array $tags): void
    {
        $builder->where(function($query) use ($tags) {
            foreach ($tags as $tag) {
                $query->orWhereJsonContains('tags', $tag);
            }
        });
    }

    public function userId(Builder $builder, int $user_id): void
    {
        $builder->whereUserId($user_id);
    }
}