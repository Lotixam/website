<?php

namespace App\Models;

use App\Enums\DocumentRequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequest extends Model
{
    protected $fillable = [
        'operation_id',
        'operation_workflow_node_id',
        'stage_id',
        'requested_by_user_id',
        'assigned_to_user_id',
        'name',
        'description',
        'status',
        'due_date',
        'document_id',
        'reviewed_at',
        'reviewed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => DocumentRequestStatus::class,
            'due_date' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function workflowNode(): BelongsTo
    {
        return $this->belongsTo(OperationWorkflowNode::class, 'operation_workflow_node_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
