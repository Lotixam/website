<?php

namespace App\Filament\Widgets;

use App\Models\Lot;
use App\Models\Operation;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeOperations = Operation::whereNotIn('status', ['completed', 'cancelled'])->count();
        $availableLots = Lot::where('status', 'available')->count();
        $totalIncome = Transaction::where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('type', 'expense')->sum('amount');
        $margin = $totalIncome - $totalExpense;

        return [
            Stat::make('Opérations en cours', $activeOperations)
                ->description('Actives')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
            Stat::make('Lots disponibles', $availableLots)
                ->description('À vendre')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('success'),
            Stat::make('Chiffre d\'affaires', number_format($totalIncome, 0, ',', ' ') . ' €')
                ->description('Total entrées')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Marge globale', number_format($margin, 0, ',', ' ') . ' €')
                ->description('Entrées - Sorties')
                ->descriptionIcon($margin >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($margin >= 0 ? 'success' : 'danger'),
        ];
    }
}
