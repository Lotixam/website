<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BlogPublicFileController extends Controller
{
    /**
     * Sert les médias du blog (hors lien symbolique public/storage si l’hébergeur renvoie 403).
     * Seuls les chemins sous blog/content/ et blog/covers/ sont autorisés.
     */
    public function show(string $path): Response|StreamedResponse
    {
        $path = ltrim($path, '/');

        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }

        if (! preg_match('#^blog/(content|covers)/#', $path)) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
