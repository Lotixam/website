<?php

namespace App\Models;

use App\Enums\WorkflowParticipantVisibility;
use App\Enums\WorkflowValidationPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowTemplateNode extends Model
{
    protected $fillable = [
        'workflow_template_id',
        'parent_id',
        'parallel_group',
        'is_merge_node',
        'sort_order',
        'title',
        'description',
        'validation_policy',
        'participant_visibility',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_merge_node' => 'boolean',
            'sort_order' => 'integer',
            'validation_policy' => WorkflowValidationPolicy::class,
            'participant_visibility' => WorkflowParticipantVisibility::class,
            'metadata' => 'array',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }
}
