<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreApiTokenRequest;
use App\Http\Resources\Api\PersonalAccessTokenResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TokenController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $tokens = $request->user()
            ->tokens()
            ->orderByDesc('created_at')
            ->get();

        return PersonalAccessTokenResource::collection($tokens);
    }

    public function store(StoreApiTokenRequest $request): JsonResponse
    {
        $newToken = $request->user()->createToken($request->validated('device_name'), ['*']);

        return response()->json([
            'token' => $newToken->plainTextToken,
            'token_id' => $newToken->accessToken->id,
            'token_resource' => new PersonalAccessTokenResource($newToken->accessToken),
        ], 201);
    }

    public function destroy(Request $request, int|string $id): JsonResponse
    {
        $deleted = $request->user()
            ->tokens()
            ->whereKey($id)
            ->delete();

        if ($deleted === 0) {
            abort(404);
        }

        return response()->json(['message' => 'Jeton révoqué.']);
    }
}
