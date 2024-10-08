<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Text\StoreRequest;
use App\Http\Requests\Text\UpdateRequest;
use App\Services\TextService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TextController extends Controller
{
    private $textService;

    public function __construct(TextService $textService)
    {
        $this->textService = $textService;
    }
    
    public function index()
    {
        $texts = $this->textService->getAllTexts();
        return response()->json([
            'status' => 'ok',
            'data' => $texts,
        ], 200);
    }
    
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $text = $this->textService->store($data);

        return response()->json([
            'status' => 'ok',
            'slug' => $text->slug,
        ], 201);
    }

    
    public function show($slug)
    {
        $text = $this->textService->getText($slug);

        if ($text === null) {
            throw new NotFoundHttpException('Text not found');
        }

        return response()->json([
            'status' => 'ok',
            'data' => $text,
        ]);
    }

    public function update(UpdateRequest $request, $slug)
    {
        $data = $request->validated();

        $text = $this->textService->update($data, $slug);
        if ($text === null) {
            throw new NotFoundHttpException('Text not found');
        }

        return response()->json([
            'status' => 'ok',
            'data' => $text,
        ]);
    }

    
    public function destroy($slug)
    {
        $text = $this->textService->delete($slug);
        if ($text === null) {
            throw new NotFoundHttpException('Text not found');
        }

        return response()->json([
            'status' => 'ok',
            'data' => $text,
        ]);
    }
}
