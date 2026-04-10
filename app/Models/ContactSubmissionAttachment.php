<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ContactSubmissionAttachment extends Model
{
    protected $fillable = [
        'contact_submission_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $attachment): void {
            if ($attachment->path && Storage::disk($attachment->disk)->exists($attachment->path)) {
                Storage::disk($attachment->disk)->delete($attachment->path);
            }
        });
    }

    public function contactSubmission(): BelongsTo
    {
        return $this->belongsTo(ContactSubmission::class);
    }
}
