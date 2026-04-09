<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stage extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'order',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function operations(): BelongsToMany
    {
        return $this->belongsToMany(Operation::class, 'operation_stage')
            ->withPivot(['status', 'completed_at', 'notes'])
            ->withTimestamps();
    }
}
