<?php

namespace App\Models;

use App\Enums\PublicMetricSource;
use Illuminate\Database\Eloquent\Model;

class PublicMetric extends Model
{
    protected $fillable = [
        'label',
        'source',
        'value_override',
        'suffix',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'source' => PublicMetricSource::class,
            'is_visible' => 'boolean',
        ];
    }
}
