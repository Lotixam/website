<?php

namespace App\Models;

use App\Enums\ContactType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Contact extends Model
{
    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'postal_code',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContactType::class,
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function operationsAsSeller(): HasMany
    {
        return $this->hasMany(Operation::class, 'seller_contact_id');
    }

    public function lotsAsBuyer(): HasMany
    {
        return $this->hasMany(Lot::class, 'buyer_contact_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
