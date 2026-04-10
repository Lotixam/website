<?php

namespace Tests\Feature;

use App\Models\PublicRealization;
use App\Models\PublicRealizationSlide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRealizationsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_nos_realisations_returns_ok(): void
    {
        $response = $this->get('/nos-realisations');

        $response->assertOk();
        $response->assertSee('NOS RÉALISATIONS', false);
    }

    public function test_nos_realisations_shows_visible_realization_title(): void
    {
        $realization = PublicRealization::query()->create([
            'title' => 'Résidence Test Plan',
            'excerpt' => null,
            'body' => null,
            'highlights' => null,
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        PublicRealizationSlide::query()->create([
            'public_realization_id' => $realization->id,
            'image_path' => 'realizations/slides/placeholder.jpg',
            'caption' => null,
            'sort_order' => 1,
        ]);

        $response = $this->get('/nos-realisations');

        $response->assertOk();
        $response->assertSee('Résidence Test Plan', false);
    }

    public function test_hidden_realization_not_listed(): void
    {
        PublicRealization::query()->create([
            'title' => 'Projet caché',
            'excerpt' => null,
            'body' => null,
            'highlights' => null,
            'is_visible' => false,
            'sort_order' => 1,
        ]);

        $response = $this->get('/nos-realisations');

        $response->assertOk();
        $response->assertDontSee('Projet caché', false);
    }
}
