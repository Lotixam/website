<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'espinar.lucas0@gmail.com'],
            [
                'name' => 'Lucas Espinar',
                'username' => 'admin',
                'password' => bcrypt('Lapino1407+'),
            ]
        );

        $user->assignRole('admin');
    }
}
