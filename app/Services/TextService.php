<?php

namespace App\Services;

use App\Jobs\DeleteTextJob;
use App\Models\Text;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TextService
{
    public function getAllTexts(): Collection
    {
        $texts = Text::public()->paginate(15);
        return new Collection($texts);
    }

    public function checkExpirationText(Text $text)
    {
        // if the text should be deleted after viewing
        if($text->expiration === null) {
            $text->delete();
        }
    }

    public function storeText($data): Text
    {
        if (Auth::check()) {
            $data['user_id'] = Auth::id();
        }
        // array to json
        if(isset($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }

        $data['expiration'] = $this->getNowAddMinutes($data['expiration']);
        
        $text = Text::create($data);
        
        if($text->expiration != null) {
            DeleteTextJob::dispatch($text->id)->delay($text->expiration);
        }

        return $text;
    }

    public function updateText($data, Text $text)
    {
        // array to json
        if(isset($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }

        if(isset($data['expiration'])) {
            $data['expiration'] = $this->getNowAddMinutes($data['expiration']);
            if($data['expiration'] != null) {
                DeleteTextJob::dispatch($text->id)->delay($text->expiration);
            }
        }

        $text->update($data);
    }

    public function deleteText(Text $text)
    {
        $text->delete();
    }

    private function getNowAddMinutes(int $expiration): Carbon|null
    {
        return $expiration != 0 ? 
            now()->addMinutes($expiration) :
            null;
    }
}