<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        $agents = [
            [
                'code' => 'AG-001',
                'name' => 'Agent Internal',
                'phone' => '081234567890',
                'email' => 'agent.internal@example.com',
                'address' => 'Denpasar, Bali',
                'status' => 'active',
            ],
            [
                'code' => 'AG-002',
                'name' => 'Bali Travel Agent',
                'phone' => '081234567891',
                'email' => 'info@balitravel.example',
                'address' => 'Sanur, Bali',
                'status' => 'active',
            ],
            [
                'code' => 'AG-003',
                'name' => 'Lake Tour Agent',
                'phone' => '081234567892',
                'email' => 'info@laketour.example',
                'address' => 'Bangli, Bali',
                'status' => 'active',
            ],
            [
                'code' => 'AG-004',
                'name' => 'Demo Agent',
                'phone' => '081234567893',
                'email' => 'demo@example.com',
                'address' => 'Bali',
                'status' => 'inactive',
            ],
        ];

        foreach ($agents as $agent) {
            Agent::updateOrCreate(
                ['code' => $agent['code']],
                array_merge($agent, [
                    'uuid' => Str::uuid(),
                ])
            );
        }
    }
}