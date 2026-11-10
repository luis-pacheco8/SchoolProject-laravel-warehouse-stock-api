<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@armazem.pt',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Operador',
            'email'    => 'operador@armazem.pt',
            'password' => Hash::make('password123'),
            'role'     => 'operator',
        ]);

        $this->call([
            LocationSeeder::class,
            ProductSeeder::class,
            MovementSeeder::class,
        ]);
    }
}