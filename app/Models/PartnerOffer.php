<?php

namespace App\Models;

use App\Enums\OfferStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerOffer extends Model
{
    protected $fillable = [
        'partner_id',
        'lot_id',
        'description',
        'price',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => OfferStatus::class,
            'price' => 'decimal:2',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }
}
