<?php

namespace Database\Seeders;

use App\Models\LeadStage;
use Illuminate\Database\Seeder;

class LeadStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            [
                'name' => 'New',
                'slug' => 'new',
                'sort_order' => 10,
                'probability' => 10,
            ],
            [
                'name' => 'Contacted',
                'slug' => 'contacted',
                'sort_order' => 20,
                'probability' => 25,
            ],
            [
                'name' => 'Qualified',
                'slug' => 'qualified',
                'sort_order' => 30,
                'probability' => 50,
            ],
            [
                'name' => 'Proposal',
                'slug' => 'proposal',
                'sort_order' => 40,
                'probability' => 70,
            ],
            [
                'name' => 'Negotiation',
                'slug' => 'negotiation',
                'sort_order' => 50,
                'probability' => 85,
            ],
            [
                'name' => 'Won',
                'slug' => 'won',
                'sort_order' => 60,
                'probability' => 100,
                'is_won' => true,
            ],
            [
                'name' => 'Lost',
                'slug' => 'lost',
                'sort_order' => 70,
                'probability' => 0,
                'is_lost' => true,
            ],
        ];

        foreach ($stages as $stage) {
            LeadStage::updateOrCreate(
                ['slug' => $stage['slug']],
                [
                    ...$stage,
                    'is_won' => $stage['is_won'] ?? false,
                    'is_lost' => $stage['is_lost'] ?? false,
                    'active' => true,
                ]
            );
        }
    }
}
