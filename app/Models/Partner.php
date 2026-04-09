<?php

namespace App\Models;

use App\Enums\PartnerType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'type',
        'contact_name',
        'email',
        'phone',
        'website',
        'description',
        'logo_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => PartnerType::class,
            'is_active' => 'boolean',
        ];
    }

    public function offers(): HasMany
    {
        return $this->hasMany(PartnerOffer::class);
    }
}
