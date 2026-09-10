<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Boat;

class BoatSeeder extends Seeder
{
    public function run(): void
    {
        Boat::updateOrCreate(
            ['code' => 'BT-001'],
            [
                'name' => 'Boat 01',
                'registration_number' => null,
                'capacity' => 10,
                'status' => 'active',
                'operational_status' => 'available',
                'description' => 'Boat wisata',
            ]
        );

        Boat::updateOrCreate(
            ['code' => 'BT-002'],
            [
                'name' => 'Boat 02',
                'registration_number' => null,
                'capacity' => 10,
                'status' => 'active',
                'operational_status' => 'available',
                'description' => 'Boat wisata',
            ]
        );
    }
}