<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Estimate;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Requirement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $user = User::factory()->create();

        $client = Client::create([
            'client_number' => 'CLI-'.$this->faker
                ->unique()
                ->numerify('#####'),

            'name' => $this->faker->company(),

            'status' => 'active',

            'created_by' => $user->id,
        ]);

        $requirement =
            Requirement::create([
                'requirement_number' => 'REQ-'.$this->faker
                    ->unique()
                    ->numerify('#####'),

                'client_id' => $client->id,

                'title' => $this->faker->sentence(3),

                'status' => 'approved',

                'created_by' => $user->id,
            ]);

        $version =
            $requirement
                ->versions()
                ->create([
                    'version_number' => 1,

                    'status' => 'approved',

                    'created_by' => $user->id,
                ]);

        $estimate =
            Estimate::create([
                'estimate_number' => 'EST-'.$this->faker
                    ->unique()
                    ->numerify('#####'),

                'requirement_version_id' => $version->id,

                'status' => 'approved',

                'currency' => 'LKR',

                'estimated_revenue' => 100000,

                'created_by' => $user->id,
            ]);

        $quotation =
            Quotation::create([
                'quotation_number' => 'QTN-'.$this->faker
                    ->unique()
                    ->numerify('#####'),

                'client_id' => $client->id,

                'requirement_version_id' => $version->id,

                'estimate_id' => $estimate->id,

                'status' => 'accepted',

                'currency' => 'LKR',

                'subtotal' => 100000,

                'total_amount' => 100000,

                'valid_until' => now()->addDays(30),

                'created_by' => $user->id,
            ]);

        $contract =
            Contract::create([
                'contract_number' => 'CON-'.$this->faker
                    ->unique()
                    ->numerify('#####'),

                'quotation_id' => $quotation->id,

                'client_id' => $client->id,

                'requirement_version_id' => $version->id,

                'estimate_id' => $estimate->id,

                'status' => 'signed',

                'title' => 'Software Services Contract',

                'currency' => 'LKR',

                'contract_value' => 100000,

                'created_by' => $user->id,

                'signed_at' => now(),
            ]);

        return [
            'project_number' => 'PRJ-'.$this->faker
                ->unique()
                ->numerify('#####'),

            'contract_id' => $contract->id,

            'client_id' => $client->id,

            'requirement_version_id' => $version->id,

            'estimate_id' => $estimate->id,

            'name' => $this->faker->sentence(3),

            'status' => 'planned',

            'health' => 'green',

            'methodology' => 'Agile',

            'currency' => 'LKR',

            'contract_value' => 100000,

            'project_manager_id' => $user->id,

            'created_by' => $user->id,
        ];
    }
}
