<?php

namespace App\Http\Controllers;

use App\Http\Requests\NovelStoreRequest;
use App\Http\Resources\NovelResource;
use App\Models\Novel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NovelController extends Controller
{
  public function store(NovelStoreRequest $request): JsonResponse
  {
    $data = $request->validated();

    $novel = Novel::create([
      'user_id' => auth()->id(),
      'title' => $data['title'],
      'synopsis' => $data['synopsis']
    ]);

    return response()->json([
      'message' => 'Novel created successfully.',
      'data' => new NovelResource($novel)
    ], 201);
  }
}
