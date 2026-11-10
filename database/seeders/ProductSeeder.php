<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        
        Product::create(['name' => 'Portátil Pro 15',
            'sku' => 'LAP-PRO-15',
              'min_stock' => 10]);

        
        Product::create(['name' => 'Rato Sem Fios',
              'sku' => 'MOUSE-WL-01',
               'min_stock' => 50]);

       
        Product::create(['name' => 'Teclado Mecânico',
           'sku' => 'KEY-MECH-01',
            'min_stock' => 20]);

       
        Product::create(['name' => 'Monitor 4K 27"',
             'sku' => 'MON-4K-27',
                'min_stock' => 5]);
    }
}