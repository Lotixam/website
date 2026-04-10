<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationProposal extends Model
{
    protected $fillable = [
        'operation_id',
        'operation_workflow_node_id',
        'amount',
        'notes',
        'status',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'responded_at' => 'datetime',
        ];
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function workflowNode(): BelongsTo
    {
        return $this->belongsTo(OperationWorkflowNode::class, 'operation_workflow_node_id');
    }
}
