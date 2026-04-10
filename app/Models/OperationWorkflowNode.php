<?php

namespace App\Models;

use App\Enums\WorkflowNodeStatus;
use App\Enums\WorkflowParticipantVisibility;
use App\Enums\WorkflowValidationPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperationWorkflowNode extends Model
{
    protected $fillable = [
        'operation_id',
        'workflow_template_node_id',
        'parent_id',
        'parallel_group',
        'is_merge_node',
        'sort_order',
        'title',
        'description',
        'validation_policy',
        'participant_visibility',
        'status',
        'blocked_by_node_id',
        'started_at',
        'completed_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_merge_node' => 'boolean',
            'sort_order' => 'integer',
            'validation_policy' => WorkflowValidationPolicy::class,
            'participant_visibility' => WorkflowParticipantVisibility::class,
            'status' => WorkflowNodeStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function templateNode(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplateNode::class, 'workflow_template_node_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'blocked_by_node_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(OperationWorkflowApproval::class);
    }

    public function documentRequests(): HasMany
    {
        return $this->hasMany(DocumentRequest::class);
    }
}
