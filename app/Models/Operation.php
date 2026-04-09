<?php

namespace App\Models;

use App\Enums\OperationStatus;
use App\Enums\OperationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    ];

    protected function casts(): array
    {
        return [
            'type' => OperationType::class,
            'status' => OperationStatus::class,
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'estimated_resale_total' => 'decimal:2',
            'total_surface' => 'decimal:2',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'seller_contact_id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function stages(): BelongsToMany
    {
        return $this->belongsToMany(Stage::class, 'operation_stage')
            ->withPivot(['status', 'completed_at', 'notes'])
            ->withTimestamps()
            ->orderBy('stages.order');
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
