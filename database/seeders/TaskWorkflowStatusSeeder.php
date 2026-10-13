<?php

namespace Database\Seeders;

use App\Models\TaskWorkflowStatus;
use Illuminate\Database\Seeder;

class TaskWorkflowStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Backlog',
                'slug' => 'backlog',
                'sort_order' => 10,
                'is_initial' => true,
            ],
            [
                'name' => 'Ready',
                'slug' => 'ready',
                'sort_order' => 20,
            ],
            [
                'name' => 'In Progress',
                'slug' => 'in-progress',
                'sort_order' => 30,
                'is_in_progress' => true,
            ],
            [
                'name' => 'Review',
                'slug' => 'review',
                'sort_order' => 40,
                'is_review' => true,
            ],
            [
                'name' => 'Changes Required',
                'slug' => 'changes-required',
                'sort_order' => 50,
            ],
            [
                'name' => 'Done',
                'slug' => 'done',
                'sort_order' => 60,
                'is_completed' => true,
            ],
            [
                'name' => 'Blocked',
                'slug' => 'blocked',
                'sort_order' => 70,
            ],
        ];

        foreach ($statuses as $status) {
            TaskWorkflowStatus::updateOrCreate(
                [
                    'slug' => $status['slug'],
                ],
                [
                    ...$status,
                    'active' => true,
                ]
            );
        }
    }
}
