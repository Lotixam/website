<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PublicRealizationSlide extends Model
{
    protected $fillable = [
        'public_realization_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function realization(): BelongsTo
    {
        return $this->belongsTo(PublicRealization::class, 'public_realization_id');
    }

    public function imageUrl(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }
}
