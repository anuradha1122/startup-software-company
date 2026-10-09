<?php

use App\Models\CompanySetting;
use App\Models\User;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
    ]);
});

it('allows admin to update company settings', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $this
        ->actingAs($admin)
        ->put('/admin/company', [
            'legal_name' => 'IT Hub Private Limited',
            'trading_name' => 'IT Hub',
            'registration_number' => 'PV12345',
            'tax_number' => 'TIN12345',
            'email' => 'info@ithub.lk',
            'phone' => '0112345678',
            'website' => 'https://example.com',
            'address' => 'Colombo, Sri Lanka',
            'country' => 'Sri Lanka',
            'currency' => 'LKR',
            'timezone' => 'Asia/Colombo',
            'bank_name' => 'Example Bank',
            'bank_branch' => 'Colombo',
            'bank_account_name' => 'IT Hub',
            'bank_account_number' => '123456789',
            'bank_swift_code' => 'TESTLKLX',
            'document_footer' => 'Thank you.',
        ])
        ->assertRedirect();

    $setting = CompanySetting::firstOrFail();

    expect($setting->legal_name)
        ->toBe('IT Hub Private Limited');

    expect($setting->bank_account_number)
        ->toBe('123456789');

    $raw = DB::table('company_settings')
        ->value('bank_account_number');

    expect($raw)
        ->not->toBe('123456789');
});
