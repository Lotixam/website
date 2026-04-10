<?php

namespace Database\Seeders;

use App\Enums\PublicMetricSource;
use App\Models\PublicMetric;
use Illuminate\Database\Seeder;

class PublicMetricSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'label' => 'Opérations réussies',
                'source' => PublicMetricSource::OperationsCompleted->value,
                'suffix' => '+',
                'sort_order' => 1,
                'is_visible' => true,
            ],
            [
                'label' => 'Biens vendus',
                'source' => PublicMetricSource::LotsSold->value,
                'suffix' => '+',
                'sort_order' => 2,
                'is_visible' => true,
            ],
            [
                'label' => 'Opérations en cours',
                'source' => PublicMetricSource::OperationsInProgress->value,
                'suffix' => '',
                'sort_order' => 3,
                'is_visible' => false,
            ],
        ];

        foreach ($defaults as $metric) {
            PublicMetric::query()->firstOrCreate(
                [
                    'label' => $metric['label'],
                    'source' => $metric['source'],
                ],
                $metric
            );
        }
    }
}
