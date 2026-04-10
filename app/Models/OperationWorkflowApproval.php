<?php

namespace App\Models;

use App\Enums\WorkflowApprovalState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationWorkflowApproval extends Model
{
    protected $fillable = [
        'operation_workflow_node_id',
        'user_id',
        'actor_role',
        'state',
        'comment',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'state' => WorkflowApprovalState::class,
            'decided_at' => 'datetime',
        ];
    }

    public function node(): BelongsTo
    {
        return $this->belongsTo(OperationWorkflowNode::class, 'operation_workflow_node_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
