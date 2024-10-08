<?php

namespace App\Policies;

use App\Models\Text;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TextPolicy
{
    use HandlesAuthorization;

    public function view(User $user = null, Text $text)
    {
        if($text->is_public) {
            return true;
        }
        return $user != null ? $text->user_id === $user->id : false;
    }

    public function update(User $user, Text $text)
    {
        return $text->user_id === $user->id;
    }

    public function delete(User $user, Text $text)
    {
        return $text->user_id === $user->id;
    }
}
