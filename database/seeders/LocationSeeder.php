<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        Location::create(['name' => 'Armazém Principal — Corredor A', 'code' => 'A-01']);
        Location::create(['name' => 'Armazém Principal — Corredor B', 'code' => 'A-02']);
        Location::create(['name' => 'Armazém Secundário — Corredor A', 'code' => 'B-01']);
        Location::create(['name' => 'Zona de Expedição',              'code' => 'EXP-01']);
    }
}