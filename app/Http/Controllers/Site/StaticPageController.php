<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class StaticPageController extends Controller
{
    private const ALLOWED = [
        'index',
        'about',
        'seller',
        'buyer',
        'investor',
        'legals',
        'contributors',
        'simulation',
    ];

    public function home(): View
    {
        return view('vitrine.index');
    }

    public function show(string $vitrinePage): View
    {
        abort_unless(in_array($vitrinePage, self::ALLOWED, true), 404);

        return view('vitrine.'.$vitrinePage);
    }
}
