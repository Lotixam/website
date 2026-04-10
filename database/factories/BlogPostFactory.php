<?php

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 999999),
            'excerpt' => fake()->optional()->paragraph(),
            'content' => [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [
                            ['type' => 'text', 'text' => fake()->paragraph()],
                        ],
                    ],
                ],
            ],
            'published_at' => now()->subDay(),
            'is_visible' => true,
            'sort_order' => 1,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'published_at' => null,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (): array => [
            'is_visible' => false,
        ]);
    }
}
