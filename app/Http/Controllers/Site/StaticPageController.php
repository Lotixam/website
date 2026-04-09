<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StaticPageController extends Controller
{
    private const ALLOWED = [
        'index.html',
        'about.html',
        'seller.html',
        'buyer.html',
        'investor.html',
        'legals.html',
        'contributors.html',
        'simulation.html',
    ];

    public function home(): BinaryFileResponse
    {
        return $this->serve('index.html');
    }

    public function show(string $vitrinePage): BinaryFileResponse
    {
        return $this->serve($vitrinePage);
    }

    private function serve(string $file): BinaryFileResponse
    {
        abort_unless(in_array($file, self::ALLOWED, true), 404);

        $path = public_path('html/'.$file);

        abort_unless(is_file($path), 404);

        return response()->file($path);
    }
}
