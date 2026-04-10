<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\PublicMetric;
use App\Services\PublicMetricValueResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class StaticPageController extends Controller
{
    private const ALLOWED = [
        'index',
        'about',
        'seller',
        'buyer',
        'investor',
        'legals',
        'cookies',
        'contributors',
        'simulation',
    ];

    public function home(): View
    {
        return view('vitrine.index', [
            'publicMetrics' => $this->publicMetrics(),
        ]);
    }

    public function show(string $vitrinePage): View
    {
        abort_unless(in_array($vitrinePage, self::ALLOWED, true), 404);

        return view('vitrine.'.$vitrinePage);
    }

    private function publicMetrics(): Collection
    {
        $resolver = app(PublicMetricValueResolver::class);

        return PublicMetric::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (PublicMetric $metric) use ($resolver): array {
                return [
                    'label' => $metric->label,
                    'value' => number_format($resolver->resolve($metric), 0, ',', ' '),
                    'suffix' => $metric->suffix ?? '',
                ];
            });
    }
}
