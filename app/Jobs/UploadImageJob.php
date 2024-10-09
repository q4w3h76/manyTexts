<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }
    
    public function handle()
    {
        $file = Storage::get($this->path);
        
        if($file != null)
            $status = Storage::disk('s3')->put($this->path, $file);
        
        if (!$status) {
            $this->fail('Failed upload image to ' . $this->path);
        } else {
            Storage::delete($this->path);
        }
    }
}
