<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactSubmission extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'source_page',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(ContactSubmissionAttachment::class);
    }
}
