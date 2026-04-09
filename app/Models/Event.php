<?php

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'operation_id',
        'lot_id',
        'contact_id',
        'title',
        'description',
        'start_at',
        'end_at',
        'type',
        'is_completed',
    ];

    protected function casts(): array
    {
        return [
            'type' => EventType::class,
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_completed' => 'boolean',
        ];
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
