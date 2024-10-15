<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Text\FilterRequest;
use App\Http\Requests\Text\StoreRequest;
use App\Http\Requests\Text\UpdateRequest;
use App\Http\Resources\TextCollection;
use App\Http\Resources\TextResource;
use App\Models\Text;
use App\Models\User;
use App\Services\TextService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TextController extends Controller
{
    private $textService;

    public function __construct(TextService $textService)
    {
        $this->textService = $textService;
    }
    
    public function index(FilterRequest $request)
    {
        $data = $request->validated();
        $texts = $this->textService->getAllTexts($data);

        return TextCollection::make($texts)->resolve();
    }
    
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $text = $this->textService->storeText($data);

        return TextResource::make($text);
    }
    
    public function show(string $slug)
    {
        $text = $this->textService->getText($slug);

        if($text === null)
            throw new NotFoundHttpException('Text not found');

        $this->authorize('view', $text);

        return TextResource::make($text);
    }

    public function update(UpdateRequest $request, Text $text)
    {
        $data = $request->validated();
        
        $this->textService->updateText($data, $text);

        return TextResource::make($text);
    }

    
    public function destroy(Text $text)
    {
        $this->textService->deleteText($text);

        return response()->json([], 204);
    }
}
