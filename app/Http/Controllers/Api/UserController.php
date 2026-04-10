<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) $request->input('per_page', 15);

        $query = User::query()
            ->with(['profile', 'roles'])
            ->orderBy('name');

        return UserResource::collection($query->paginate($perPage));
    }

    public function show(User $user): UserResource
    {
        $user->load(['profile', 'roles']);

        return new UserResource($user);
    }
}
