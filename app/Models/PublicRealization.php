<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublicRealization extends Model
{
    protected $fillable = [
        'title',
        'excerpt',
        'body',
        'highlights',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'is_visible' => 'boolean',
        ];
    }

    public function slides(): HasMany
    {
        return $this->hasMany(PublicRealizationSlide::class);
    }
}
