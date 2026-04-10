<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\OperationResource;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OperationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();
        $this->authorize('viewAny', Operation::class);

        $request->validate([
            'updated_since' => ['sometimes', 'date'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Operation::query()->orderByDesc('updated_at');

        if ($this->collaboratorSeesOnlyAssignedOperations($user)) {
            $query->whereHas('assignedUsers', fn ($q) => $q->where('user_id', $user->id));
        }

        if ($request->filled('updated_since')) {
            $query->where('updated_at', '>=', $request->date('updated_since'));
        }

        $perPage = (int) $request->input('per_page', 15);

        return OperationResource::collection($query->paginate($perPage));
    }

    public function show(Request $request, Operation $operation): OperationResource
    {
        $this->authorize('view', $operation);

        return new OperationResource($operation);
    }

    private function collaboratorSeesOnlyAssignedOperations(User $user): bool
    {
        return $user->hasRole('collaborator') && ! $user->hasRole('admin');
    }
}
