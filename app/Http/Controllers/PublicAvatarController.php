<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicAvatarController extends Controller
{
    /**
     * Sert les fichiers du disque public/avatars sans passer par public/storage.
     * Sur mutualisés où FollowSymLinks est désactivé, /storage/... renvoie souvent 403.
     */
    public function show(string $filename): StreamedResponse
    {
        if (! preg_match('/^[a-zA-Z0-9_-]{8,255}\.[a-zA-Z0-9]{2,8}$/', $filename)) {
            abort(404);
        }

        $path = 'avatars/'.$filename;

        if (! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
}
