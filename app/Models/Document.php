<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'name',
        'type',
        'file_path',
        'file_size',
        'mime_type',
        'notes',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'uploaded_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
