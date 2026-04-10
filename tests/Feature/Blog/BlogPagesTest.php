<?php

namespace Tests\Feature\Blog;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_returns_ok(): void
    {
        $response = $this->get(route('blog.index'));

        $response->assertOk();
        $response->assertSee('Le blog', false);
    }

    public function test_blog_index_shows_published_visible_post(): void
    {
        $post = BlogPost::factory()->create([
            'title' => 'Article vitrine test',
            'slug' => 'article-vitrine-test',
            'published_at' => now()->subHour(),
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('blog.index'));

        $response->assertOk();
        $response->assertSee('Article vitrine test', false);
        $response->assertSee(route('blog.show', ['slug' => $post->slug]), false);
    }

    public function test_draft_post_not_on_index(): void
    {
        BlogPost::factory()->draft()->create([
            'title' => 'Brouillon secret',
            'slug' => 'brouillon-secret',
            'sort_order' => 1,
        ]);

        $response = $this->get(route('blog.index'));

        $response->assertOk();
        $response->assertDontSee('Brouillon secret', false);
    }

    public function test_hidden_post_not_on_index(): void
    {
        BlogPost::factory()->hidden()->create([
            'title' => 'Article masqué',
            'slug' => 'article-masque',
            'sort_order' => 1,
        ]);

        $response = $this->get(route('blog.index'));

        $response->assertOk();
        $response->assertDontSee('Article masqué', false);
    }

    public function test_show_returns_ok_for_published_post(): void
    {
        BlogPost::factory()->create([
            'title' => 'Lecture article',
            'slug' => 'lecture-article',
            'published_at' => now()->subDay(),
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('blog.show', ['slug' => 'lecture-article']));

        $response->assertOk();
        $response->assertSee('Lecture article', false);
        $response->assertSee('Rédaction : Lotixam SAS', false);
    }

    public function test_show_displays_author_byline_when_set(): void
    {
        BlogPost::factory()->create([
            'title' => 'Article signé',
            'slug' => 'article-signe',
            'author_first_name' => 'Marie',
            'author_last_name' => 'Dupont',
            'published_at' => now()->subDay(),
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('blog.show', ['slug' => 'article-signe']));

        $response->assertOk();
        $response->assertSee('Rédaction : Marie Dupont', false);
        $response->assertDontSee('Rédaction : Lotixam SAS', false);
    }

    public function test_show_draft_returns_404(): void
    {
        BlogPost::factory()->draft()->create([
            'title' => 'Pas public',
            'slug' => 'pas-public',
            'sort_order' => 1,
        ]);

        $response = $this->get(route('blog.show', ['slug' => 'pas-public']));

        $response->assertNotFound();
    }

    public function test_search_filters_by_title(): void
    {
        BlogPost::factory()->create([
            'title' => 'Alpha immobilier unique',
            'slug' => 'alpha-immobilier',
            'published_at' => now()->subDay(),
            'is_visible' => true,
            'sort_order' => 1,
        ]);
        BlogPost::factory()->create([
            'title' => 'Sujets divers sans mot cle',
            'slug' => 'sujets-divers',
            'published_at' => now()->subDay(),
            'is_visible' => true,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('blog.index', ['q' => 'Alpha immobilier']));

        $response->assertOk();
        $response->assertSee('Alpha immobilier unique', false);
        $response->assertDontSee('Sujets divers sans mot cle', false);
    }

    public function test_month_filter_limits_posts(): void
    {
        $year = (int) now()->year;

        BlogPost::factory()->create([
            'title' => 'Janvier',
            'slug' => 'post-janvier',
            'published_at' => Carbon::create($year, 1, 15, 12, 0, 0),
            'is_visible' => true,
            'sort_order' => 1,
        ]);
        BlogPost::factory()->create([
            'title' => 'Mars',
            'slug' => 'post-mars',
            'published_at' => Carbon::create($year, 3, 10, 12, 0, 0),
            'is_visible' => true,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('blog.index', ['year' => $year, 'month' => 1]));

        $response->assertOk();
        $response->assertSee('Janvier', false);
        $response->assertDontSee('Mars', false);
    }
}
