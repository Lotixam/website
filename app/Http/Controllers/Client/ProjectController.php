<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Operation;
use App\Services\Workflow\WorkflowEngine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function show(Request $request, Operation $operation, WorkflowEngine $workflow): View
    {
        $user = $request->user();

        abort_unless(
            $operation->assignedUsers()->where('user_id', $user->id)->exists(),
            403
        );

        $operation->load([
            'stages' => fn ($q) => $q->orderBy('stages.order'),
            'documentRequests' => fn ($q) => $q->where('assigned_to_user_id', $user->id)->with(['stage', 'workflowNode']),
            'messages' => fn ($q) => $q->with('user')->latest()->limit(50),
            'lots',
        ]);

        $hasWorkflow = $operation->workflowNodes()->exists();
        $workflowNodes = $hasWorkflow
            ? $workflow->orderedVisibleNodesForDisplay($operation, $user)->load('blockedBy')
            : collect();
        $progress = $workflow->clientProgressPercent($operation, $user);

        return view('client.project.show', [
            'operation' => $operation,
            'progress' => $progress,
            'hasWorkflow' => $hasWorkflow,
            'workflowNodes' => $workflowNodes,
        ]);
    }
}
