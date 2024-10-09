<?php

namespace App\Services;

use App\Jobs\UploadImageJob;

class UploadImageService
{
    public static function upload($file, $path = 'images/'): string
    {
         // upload image to local storage
         $filename = $file->store($path);
         // upload avatar to s3 cloud and delete from local storage via a queue
         UploadImageJob::dispatch($filename);
         return $filename;
    }
}