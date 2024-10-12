<?php

namespace App\Services;

use App\Http\Filters\TextFilter;
use App\Jobs\DeleteTextJob;
use App\Models\Text;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TextService
{
    public function getAllTexts($data)
    {
        $filter = app()->make(TextFilter::class, ['queryParams' => array_filter($data)]);
        // get all public text with filter and pagination
        $texts = Text::public()->filter($filter)->paginate(15);

        return $texts;
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
            $data['user_id'] = Auth::user()->id;
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