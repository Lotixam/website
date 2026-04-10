<?php

namespace App\Models;

use Database\Factories\BlogPostFactory;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPost extends Model implements HasRichContent
{
    /** @use HasFactory<BlogPostFactory> */
    use HasFactory;

    use InteractsWithRichContent;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'cover_image_path',
        'content',
        'published_at',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'published_at' => 'datetime',
            'is_visible' => 'boolean',
        ];
    }

    protected function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->json()
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsVisibility('public');
    }

    protected static function booted(): void
    {
        static::updating(function (BlogPost $post): void {
            if ($post->isDirty('cover_image_path')) {
                $previous = $post->getOriginal('cover_image_path');
                if (is_string($previous) && $previous !== '') {
                    Storage::disk('public')->delete($previous);
                }
            }
        });

        static::deleting(function (BlogPost $post): void {
            if (filled($post->cover_image_path)) {
                Storage::disk('public')->delete($post->cover_image_path);
            }
        });

        static::creating(function (BlogPost $post): void {
            if (blank($post->sort_order)) {
                $max = (int) static::query()->max('sort_order');

                $post->sort_order = $max > 0 ? $max + 1 : 1;
            }
        });

        static::saving(function (BlogPost $post): void {
            if (blank($post->slug) && filled($post->title)) {
                $post->slug = Str::slug($post->title);
            }

            if (blank($post->slug)) {
                return;
            }

            $base = $post->slug;
            $candidate = $base;
            $n = 1;

            while (static::query()
                ->where('slug', $candidate)
                ->when($post->exists, fn (Builder $q) => $q->whereKeyNot($post->getKey()))
                ->exists()) {
                $candidate = $base.'-'.$n++;
            }

            $post->slug = $candidate;
        });
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_visible', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function coverImageUrl(): ?string
    {
        if (! filled($this->cover_image_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->cover_image_path);
    }
}
