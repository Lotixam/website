<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class FinanceSummaryWidget extends ChartWidget
{
    protected ?string $heading = 'Entrées / Sorties (6 derniers mois)';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i));

        $income = $months->map(fn ($month) => (float) Transaction::where('type', 'income')
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->sum('amount'));

        $expense = $months->map(fn ($month) => (float) Transaction::where('type', 'expense')
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->sum('amount'));

        return [
            'datasets' => [
                [
                    'label' => 'Entrées',
                    'data' => $income->values()->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'Sorties',
                    'data' => $expense->values()->toArray(),
                    'backgroundColor' => 'rgba(244, 63, 94, 0.2)',
                    'borderColor' => 'rgb(244, 63, 94)',
                ],
            ],
            'labels' => $months->map(fn ($m) => $m->translatedFormat('M Y'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
