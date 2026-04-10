<?php

namespace Tests\Feature\Blog;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogPublicFileTest extends TestCase
{
    public function test_blog_public_file_rejects_path_traversal(): void
    {
        $response = $this->get(url('/fichiers-blog/blog/content/../../../.env'));

        $response->assertNotFound();
    }

    public function test_blog_public_file_rejects_non_blog_prefix(): void
    {
        $response = $this->get(url('/fichiers-blog/other/file.png'));

        $response->assertNotFound();
    }

    public function test_blog_public_file_serves_existing_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('blog/content/test-image.png', 'fake');

        $response = $this->get(route('blog.public_file', ['path' => 'blog/content/test-image.png']));

        $response->assertOk();
    }
}
