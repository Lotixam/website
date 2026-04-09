<?php

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['name' => 'Prospection', 'color' => '#6b7280', 'order' => 1],
            ['name' => 'Étude de faisabilité', 'color' => '#8b5cf6', 'order' => 2],
            ['name' => 'Offre d\'achat', 'color' => '#3b82f6', 'order' => 3],
            ['name' => 'Promesse / Compromis d\'achat', 'color' => '#06b6d4', 'order' => 4],
            ['name' => 'Permis d\'aménager', 'color' => '#f59e0b', 'order' => 5],
            ['name' => 'Bornage / Géomètre', 'color' => '#f97316', 'order' => 6],
            ['name' => 'Purge des recours', 'color' => '#ef4444', 'order' => 7],
            ['name' => 'Mise en vente des lots', 'color' => '#10b981', 'order' => 8],
            ['name' => 'Compromis de vente (lot)', 'color' => '#14b8a6', 'order' => 9],
            ['name' => 'Acte authentique (lot)', 'color' => '#22c55e', 'order' => 10],
        ];

        foreach ($stages as $stage) {
            Stage::firstOrCreate(['name' => $stage['name']], array_merge($stage, ['is_default' => true]));
        }
    }
}
