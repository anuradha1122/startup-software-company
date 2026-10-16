<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use App\Models\DocumentSequence;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    public function run(): void
    {
        CompanySetting::firstOrCreate(
            ['id' => 1],
            [
                'legal_name' => 'IT Hub',
                'trading_name' => 'IT Hub',
                'country' => 'Sri Lanka',
                'currency' => 'LKR',
                'timezone' => 'Asia/Colombo',
            ]
        );

        $sequences = [
            'lead' => 'LEAD-',
            'client' => 'CLI-',
            'requirement' => 'REQ-',
            'estimate' => 'EST-',
            'proposal' => 'PRP-',
            'quotation' => 'QTN-',
            'contract' => 'CON-',
            'project' => 'PRJ-',
            'supplier' => 'SUP-',
            'expense' => 'EXP-',
            'invoice' => 'INV-',
            'credit_note' => 'CRN-',
            'receipt' => 'RCP-',
            'purchase_order' => 'PO-',
            'client_approval' => 'APR-',
            'support_ticket' => 'SUP-',
            'change_request' => 'CR-',
        ];

        foreach ($sequences as $type => $prefix) {
            DocumentSequence::firstOrCreate(
                ['type' => $type],
                [
                    'prefix' => $prefix,
                    'next_number' => 1,
                    'padding' => 5,
                    'active' => true,
                ]
            );
        }
    }
}
