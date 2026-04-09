<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Premier utilisateur (après migrate) :
        // php artisan tinker
        // >>> \App\Models\User::create(['name'=>'Prénom','username'=>'identifiant','email'=>null,'password'=>'motdepasse']);
    }
}
