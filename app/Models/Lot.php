<?php

namespace App\Models;

use App\Enums\LotStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lot extends Model
{
    protected $fillable = [
        'operation_id',
        'lot_number',
        'surface',
        'description',
        'selling_price',
        'status',
        'buyer_contact_id',
        'sold_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => LotStatus::class,
            'selling_price' => 'decimal:2',
            'surface' => 'decimal:2',
            'sold_at' => 'date',
        ];
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'buyer_contact_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function partnerOffers(): HasMany
    {
        return $this->hasMany(PartnerOffer::class);
    }
}
