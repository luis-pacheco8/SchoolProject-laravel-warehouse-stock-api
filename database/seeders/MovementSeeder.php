<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movement;
use App\Models\User;
use Carbon\Carbon;

class MovementSeeder extends Seeder
{
    
     
    public function run(): void
    {
        $adminId = User::where('email', 'admin@armazem.pt')->first()->id;

        
        Movement::create([
            'product_id'    => 1,
            'user_id'       => $adminId,
            'type'          => 'in',
            'to_location_id' => 1, // Corredor A
            'quantity'      => 60,
            'created_at'    => Carbon::now()->subDays(30),
        ]);
        Movement::create([
            'product_id'    => 1,
            'user_id'       => $adminId,
            'type'          => 'in',
            'to_location_id' => 2, // Corredor B
            'quantity'      => 40,
            'created_at'    => Carbon::now()->subDays(20),
        ]);
        Movement::create([
            'product_id'      => 1,
            'user_id'         => $adminId,
            'type'            => 'transfer',
            'from_location_id' => 2, // Corredor B → Expedição
            'to_location_id'   => 4,
            'quantity'        => 15,
            'created_at'      => Carbon::now()->subDays(10),
        ]);
        Movement::create([
            'product_id'       => 1,
            'user_id'          => $adminId,
            'type'             => 'out',
            'from_location_id' => 4, // Saiu da Expedição
            'quantity'         => 20,
            'created_at'       => Carbon::now()->subDays(5),
        ]);
        // Ajuste manual com auditoria
        Movement::create([
            'product_id'    => 1,
            'user_id'       => $adminId,
            'type'          => 'adjust',
            'quantity'      => 5,
            'justification' => 'Contagem física revelou 5 unidades não registadas no sistema.',
            'created_at'    => Carbon::now()->subDays(2),
        ]);

       
        Movement::create([
            'product_id'     => 2,
            'user_id'        => $adminId,
            'type'           => 'in',
            'to_location_id' => 1,
            'quantity'       => 80,
            'created_at'     => Carbon::now()->subDays(25),
        ]);
        Movement::create([
            'product_id'       => 2,
            'user_id'          => $adminId,
            'type'             => 'out',
            'from_location_id' => 1,
            'quantity'         => 50,
            'created_at'       => Carbon::now()->subDays(8),
        ]);

        
        Movement::create([
            'product_id'     => 3,
            'user_id'        => $adminId,
            'type'           => 'in',
            'to_location_id' => 1,
            'quantity'       => 50,
            'created_at'     => Carbon::now()->subDays(28),
        ]);
        Movement::create([
            'product_id'     => 3,
            'user_id'        => $adminId,
            'type'           => 'in',
            'to_location_id' => 3,
            'quantity'       => 70,
            'created_at'     => Carbon::now()->subDays(21),
        ]);
        Movement::create([
            'product_id'       => 3,
            'user_id'          => $adminId,
            'type'             => 'out',
            'from_location_id' => 1,
            'quantity'         => 30,
            'created_at'       => Carbon::now()->subDays(14),
        ]);
        Movement::create([
            'product_id'      => 3,
            'user_id'         => $adminId,
            'type'            => 'transfer',
            'from_location_id' => 3,
            'to_location_id'   => 4,
            'quantity'        => 20,
            'created_at'      => Carbon::now()->subDays(7),
        ]);
        Movement::create([
            'product_id'       => 3,
            'user_id'          => $adminId,
            'type'             => 'out',
            'from_location_id' => 4,
            'quantity'         => 20,
            'created_at'       => Carbon::now()->subDays(3),
        ]);

       
    }
}