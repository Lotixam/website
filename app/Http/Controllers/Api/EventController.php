<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\EventResource;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = $request->user();
        $this->authorize('viewAny', Event::class);

        $request->validate([
            'operation_id' => ['sometimes', 'integer', 'exists:operations,id'],
            'updated_since' => ['sometimes', 'date'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Event::query()->orderByDesc('updated_at');

        if ($this->collaboratorSeesOnlyAssignedOperations($user)) {
            $query->whereHas('operation', function ($op) use ($user): void {
                $op->whereHas('assignedUsers', fn ($q) => $q->where('user_id', $user->id));
            });
        }

        if ($request->filled('operation_id')) {
            $query->where('operation_id', $request->integer('operation_id'));
        }

        if ($request->filled('updated_since')) {
            $query->where('updated_at', '>=', $request->date('updated_since'));
        }

        $perPage = (int) $request->input('per_page', 15);

        return EventResource::collection($query->paginate($perPage));
    }

    public function show(Request $request, Event $event): EventResource
    {
        $this->authorize('view', $event);

        /** @var User $user */
        $user = $request->user();
        if ($this->collaboratorSeesOnlyAssignedOperations($user) && $event->operation_id !== null) {
            $this->authorize('view', $event->operation);
        }

        return new EventResource($event);
    }

    private function collaboratorSeesOnlyAssignedOperations(User $user): bool
    {
        return $user->hasRole('collaborator') && ! $user->hasRole('admin');
    }
}
