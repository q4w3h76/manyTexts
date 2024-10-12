<?php

namespace App\Services;

use App\Http\Filters\TextFilter;
use App\Jobs\DeleteTextJob;
use App\Models\Text;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TextService
{
    private const TEXTS_PREFIX = 'texts:';
    private const EXPIRATION_TIME = 24 * 60 * 60; // 1 day

    public function getAllTexts($data)
    {
        $filter = app()->make(TextFilter::class, ['queryParams' => array_filter($data)]);
        // get all public text with filter and pagination
        $texts = Text::public()->filter($filter)->paginate(15);

        return $texts;
    }

    public function getText($slug)
    {
        $text = Cache::get(self::TEXTS_PREFIX . $slug);
        if($text === null)
        {
            $text = Text::where('slug', $slug)->first();
            if($text !== null && $text->is_public) {
                Cache::set(self::TEXTS_PREFIX . $slug, $text, self::EXPIRATION_TIME);
            }
        }

        if($text !== null) {
            $this->checkExpirationText($text);
        }

        return $text;
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

        if(Cache::get(self::TEXTS_PREFIX . $text->slug) != null) {
            Cache::set(self::TEXTS_PREFIX . $text->slug, $text, self::EXPIRATION_TIME);
        }
    }

    public function deleteText(Text $text)
    {
        $text->delete();
    }

    private function checkExpirationText(Text $text)
    {
        // if the text should be deleted after viewing
        if($text->expiration === null) {
            $text->delete();
            Cache::delete(self::TEXTS_PREFIX . $text->slug);
        }
    }

    private function getNowAddMinutes(int $expiration): Carbon|null
    {
        return $expiration != 0 ? 
            now()->addMinutes($expiration) :
            null;
    }
}