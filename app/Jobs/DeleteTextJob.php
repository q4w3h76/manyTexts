<?php

namespace App\Jobs;

use App\Models\Text;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteTextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $textId;

    public function __construct(int $textId)
    {
        $this->textId = $textId;
    }

    public function handle()
    {
        Text::destroy($this->textId);
    }
}
