<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::updateOrCreate(
            ['code' => 'SRV-001'],
            [
                'name' => 'Keliling Danau',
                'price' => 50000,
                'status' => 'active',
            ]
        );

        Service::updateOrCreate(
            ['code' => 'SRV-002'],
            [
                'name' => 'Charter Boat',
                'price' => 500000,
                'status' => 'active',
            ]
        );
    }
}