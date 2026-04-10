<?php

namespace App\Services;

use App\Enums\LotStatus;
use App\Enums\OperationStatus;
use App\Enums\PublicMetricSource;
use App\Models\Lot;
use App\Models\Operation;
use App\Models\PublicMetric;

class PublicMetricValueResolver
{
    public function resolve(PublicMetric $metric): int
    {
        if ($metric->value_override !== null) {
            return (int) $metric->value_override;
        }

        return match ($metric->source) {
            PublicMetricSource::OperationsCompleted => Operation::query()
                ->where('status', OperationStatus::Completed->value)
                ->count(),
            PublicMetricSource::LotsSold => Lot::query()
                ->where('status', LotStatus::Sold->value)
                ->count(),
            PublicMetricSource::OperationsInProgress => Operation::query()
                ->whereIn('status', [
                    OperationStatus::Prospection->value,
                    OperationStatus::Acquired->value,
                    OperationStatus::InDivision->value,
                    OperationStatus::OnSale->value,
                ])
                ->count(),
            default => 0,
        };
    }
}
