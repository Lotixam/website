<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Workflow\WorkflowEngine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, WorkflowEngine $workflow): View
    {
        $user = $request->user();

        $operations = $user->assignedOperations()
            ->withCount(['lots', 'stages', 'documentRequests', 'workflowNodes'])
            ->with(['stages' => fn ($q) => $q->orderBy('stages.order')])
            ->get()
            ->map(function ($operation) use ($user, $workflow) {
                $operation->client_progress_percent = $workflow->clientProgressPercent($operation, $user);
                $operation->completed_stages_count = $operation->stages->where('pivot.status', 'completed')->count();
                $operation->total_stages_count = $operation->stages->count();

                return $operation;
            });

        $pendingDocRequests = $user->documentRequests()
            ->where('status', 'pending')
            ->count();

        $unreadMessages = 0;
        foreach ($operations as $operation) {
            $unreadMessages += $operation->messages()
                ->where('is_read', false)
                ->where('user_id', '!=', $user->id)
                ->count();
        }

        return view('client.dashboard', compact('operations', 'pendingDocRequests', 'unreadMessages'));
    }
}
