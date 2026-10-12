<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Website',
            'Referral',
            'LinkedIn',
            'Facebook',
            'Email Campaign',
            'Phone',
            'Existing Client',
            'Event',
            'Other',
        ] as $source) {
            LeadSource::firstOrCreate([
                'name' => $source,
            ]);
        }
    }
}
