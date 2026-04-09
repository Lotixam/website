<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Achat du bien', 'type' => 'expense'],
            ['name' => 'Frais de notaire', 'type' => 'expense'],
            ['name' => 'Géomètre', 'type' => 'expense'],
            ['name' => 'Travaux', 'type' => 'expense'],
            ['name' => 'Taxes & impôts', 'type' => 'expense'],
            ['name' => 'Assurances', 'type' => 'expense'],
            ['name' => 'Frais divers', 'type' => 'expense'],
            ['name' => 'Vente de lot', 'type' => 'income'],
            ['name' => 'Commission', 'type' => 'income'],
            ['name' => 'Autres revenus', 'type' => 'income'],
        ];

        foreach ($categories as $category) {
            TransactionCategory::firstOrCreate(
                ['name' => $category['name']],
                array_merge($category, ['is_default' => true])
            );
        }
    }
}
