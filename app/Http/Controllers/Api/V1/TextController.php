<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Text\FilterRequest;
use App\Http\Requests\Text\StoreRequest;
use App\Http\Requests\Text\UpdateRequest;
use App\Http\Resources\TextCollection;
use App\Http\Resources\TextResource;
use App\Models\Text;
use App\Services\TextService;

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

        return response()->json(new TextCollection($texts), 200);
    }
    
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $text = $this->textService->storeText($data);

        return response()->json([
            'status' => 'ok',
            'slug' => new TextResource($text),
        ], 201);
    }

    
    public function show(Text $text)
    {
        $this->textService->checkExpirationText($text);

        return response()->json([
            'status' => 'ok',
            'data' => new TextResource($text),
        ]);
    }

    public function update(UpdateRequest $request, Text $text)
    {
        $data = $request->validated();
        
        $this->textService->updateText($data, $text);

        return response()->json([
            'status' => 'ok',
            'data' => new TextResource($text),
        ]);
    }

    
    public function destroy(Text $text)
    {
        $this->textService->deleteText($text);

        return response()->json([], 204);
    }
}
