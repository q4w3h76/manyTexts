<?php

namespace App\Services;

use App\Models\Text;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TextService
{
    public function getAllTexts(): Collection
    {
        $texts = Text::public()->paginate(15);
        return new Collection($texts);
    }

    public function getText($slug): Text|null
    {
        $text = Text::whereSlug($slug)->first();
        if ($text != null) {
            // if text not public
            if (!$text->is_public) {
                if (Auth::check()) {
                    // if the text does not belong to the current user
                    if($text->user_id != Auth::user()->id) {
                        throw new AuthorizationException('You do not have access to this text');
                    }
                } else {
                    throw new AuthorizationException('You do not have access to this text');
                }
            // if the text should be deleted after viewing
            } elseif($text->expiration === null) {
                $text->delete();
            }
        }
        return $text;
    }

    public function store($data): Text
    {
        if (Auth::check()) {
            $data['user_id'] = Auth::user()->id;
        }
        // array to json
        if(isset($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }
        $data['expiration'] = null;

        $text = Text::create($data);
        return $text;
    }

    public function update($data, $slug): Text|null
    {
        $text = Text::whereSlug($slug)->first();
        if ($text != null) {
            if (Auth::check() && 
                $text->user_id === Auth::user()->id
            ) {
                // array to json
                if(isset($data['tags'])) {
                    $data['tags'] = json_encode($data['tags']);
                }
                $data['expiration'] = null;

                $text = Text::create($data);
            } else {
                throw new AuthorizationException('You do not have access to this text');
            }
        }
        
        return $text;
    }

    public function delete($slug): Text|null
    {
        $text = Text::whereSlug($slug)->first();
        if ($text != null) {
            if (Auth::check() && 
                $text->user_id === Auth::user()->id
            ) {
                $text->delete();
            } else {
                throw new AuthorizationException('You do not have access to this text');
            }
        }
        return $text;
    }
}