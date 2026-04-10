<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(WorkflowTemplateNode::class)->orderBy('sort_order');
    }

    public function rootNodes(): HasMany
    {
        return $this->nodes()->whereNull('parent_id')->orderBy('sort_order');
    }
}
