<?php

namespace App\Models;

use App\Enums\OperationMission;
use App\Enums\OperationStatus;
use App\Enums\OperationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Operation extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'postal_code',
        'type',
        'total_surface',
        'purchase_price',
        'purchase_date',
        'estimated_resale_total',
        'status',
        'seller_contact_id',
        'notes',
        'parent_operation_id',
        'workflow_template_id',
        'internal_objective',
        'participant_label',
        'mission',
        'closed_at',
        'closed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => OperationType::class,
            'status' => OperationStatus::class,
            'mission' => OperationMission::class,
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'estimated_resale_total' => 'decimal:2',
            'total_surface' => 'decimal:2',
            'closed_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'seller_contact_id');
    }

    public function parentOperation(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_operation_id');
    }

    public function childOperations(): HasMany
    {
        return $this->hasMany(self::class, 'parent_operation_id');
    }

    public function workflowTemplate(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    public function workflowNodes(): HasMany
    {
        return $this->hasMany(OperationWorkflowNode::class)->orderBy('sort_order');
    }

    public function workflowRootNodes(): HasMany
    {
        return $this->workflowNodes()->whereNull('parent_id')->orderBy('sort_order');
    }

    public function workflowAuditEvents(): HasMany
    {
        return $this->hasMany(WorkflowAuditEvent::class)->orderByDesc('created_at');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(OperationProposal::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function stages(): BelongsToMany
    {
        return $this->belongsToMany(Stage::class, 'operation_stage')
            ->withPivot(['status', 'completed_at', 'notes', 'added_by_user_id', 'source'])
            ->withTimestamps()
            ->orderBy('stages.order');
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'operation_user')
            ->withPivot(['role', 'assigned_at', 'workflow_entry_node_id', 'hide_upstream_steps', 'participant_kind'])
            ->withTimestamps();
    }

    public function documentRequests(): HasMany
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function getTotalIncomeAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'income')->sum('amount');
    }

    public function getTotalExpenseAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'expense')->sum('amount');
    }

    public function getMarginAttribute(): float
    {
        return $this->total_income - $this->total_expense;
    }
}
