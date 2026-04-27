<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Kiloan
            ['name' => 'Cuci Kiloan Reguler', 'category' => 'kiloan', 'type' => 'kiloan', 'price' => 7000,  'unit' => 'kg',  'icon' => 'local_laundry_service', 'sort_order' => 1],
            ['name' => 'Cuci Kiloan Express',  'category' => 'kiloan', 'type' => 'kiloan', 'price' => 12000, 'unit' => 'kg',  'icon' => 'speed',                  'sort_order' => 2],
            ['name' => 'Cuci + Setrika',        'category' => 'kiloan', 'type' => 'kiloan', 'price' => 10000, 'unit' => 'kg',  'icon' => 'dry_cleaning',           'sort_order' => 3],
            // Satuan
            ['name' => 'Kemeja',               'category' => 'satuan', 'type' => 'satuan', 'price' => 15000, 'unit' => 'pcs', 'icon' => 'dry_cleaning',           'sort_order' => 4],
            ['name' => 'Celana Panjang',        'category' => 'satuan', 'type' => 'satuan', 'price' => 15000, 'unit' => 'pcs', 'icon' => 'checkroom',              'sort_order' => 5],
            ['name' => 'Jas / Blazer',          'category' => 'satuan', 'type' => 'satuan', 'price' => 35000, 'unit' => 'pcs', 'icon' => 'dry_cleaning',           'sort_order' => 6],
            ['name' => 'Gaun Pesta',            'category' => 'satuan', 'type' => 'satuan', 'price' => 75000, 'unit' => 'pcs', 'icon' => 'checkroom',              'sort_order' => 7],
            ['name' => 'Bed Cover Set',         'category' => 'satuan', 'type' => 'satuan', 'price' => 50000, 'unit' => 'set', 'icon' => 'bed',                    'sort_order' => 8],
            ['name' => 'Selimut',               'category' => 'satuan', 'type' => 'satuan', 'price' => 25000, 'unit' => 'pcs', 'icon' => 'bed',                    'sort_order' => 9],
            // Sepatu
            ['name' => 'Cuci Sepatu Premium',   'category' => 'sepatu', 'type' => 'satuan', 'price' => 45000, 'unit' => 'prs', 'icon' => 'hiking',                 'sort_order' => 10],
            ['name' => 'Cuci Sepatu Reguler',   'category' => 'sepatu', 'type' => 'satuan', 'price' => 25000, 'unit' => 'prs', 'icon' => 'hiking',                 'sort_order' => 11],
            // Setrika
            ['name' => 'Setrika Saja',          'category' => 'setrika', 'type' => 'kiloan', 'price' => 5000, 'unit' => 'kg',  'icon' => 'iron',                   'sort_order' => 12],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
