<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@cean.com',
            'password' => bcrypt('cean2026'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Triagem',
            'email' => 'triagem@cean.com',
            'password' => bcrypt('cean2026'),
            'role' => 'triagem',
        ]);

        User::create([
            'name' => 'Atendente',
            'email' => 'atendente@cean.com',
            'password' => bcrypt('cean2026'),
            'role' => 'atendente',
        ]);
    }
}
