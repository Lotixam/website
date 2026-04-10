<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private const MONTHS_FR = [
        1 => 'janvier',
        2 => 'février',
        3 => 'mars',
        4 => 'avril',
        5 => 'mai',
        6 => 'juin',
        7 => 'juillet',
        8 => 'août',
        9 => 'septembre',
        10 => 'octobre',
        11 => 'novembre',
        12 => 'décembre',
    ];

    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->value();
        $year = $request->filled('year') ? (int) $request->query('year') : null;
        $month = $request->filled('month') ? (int) $request->query('month') : null;

        $hasFilters = $q !== '' || ($year !== null && $year > 0) || ($month !== null && $month >= 1 && $month <= 12);

        $filtered = function ($query) use ($q, $year, $month): void {
            if ($q !== '') {
                $query->where(function ($sub) use ($q): void {
                    $sub->where('title', 'like', '%'.$q.'%')
                        ->orWhere('excerpt', 'like', '%'.$q.'%');
                });
            }
            if ($year !== null && $year > 0) {
                $query->whereYear('published_at', $year);
            }
            if ($month !== null && $month >= 1 && $month <= 12) {
                $query->whereMonth('published_at', $month);
            }
        };

        $heroQuery = BlogPost::query()->published();
        if ($hasFilters) {
            $filtered($heroQuery);
        }
        $heroPosts = $heroQuery
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $listQuery = BlogPost::query()->published()->orderBy('sort_order');
        $filtered($listQuery);
        $posts = $listQuery->get();

        $timeline = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->get()
            ->map(fn (BlogPost $post): array => [
                'year' => (int) $post->published_at->year,
                'month' => (int) $post->published_at->month,
            ])
            ->unique(fn (array $row): string => $row['year'].'-'.$row['month'])
            ->sortByDesc(fn (array $row): int => $row['year'] * 100 + $row['month'])
            ->values()
            ->map(fn (array $row): array => [
                'year' => $row['year'],
                'month' => $row['month'],
                'label' => self::MONTHS_FR[$row['month']].' '.$row['year'],
            ]);

        return view('vitrine.blog.index', [
            'heroPosts' => $heroPosts,
            'posts' => $posts,
            'timeline' => $timeline,
            'searchQuery' => $q,
            'filterYear' => $year,
            'filterMonth' => $month,
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::query()
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return view('vitrine.blog.show', [
            'post' => $post,
        ]);
    }
}
