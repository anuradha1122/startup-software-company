<?php

use App\Models\DocumentSequence;
use App\Services\DocumentNumberService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates sequential document numbers', function (): void {
    DocumentSequence::create([
        'type' => 'quotation',
        'prefix' => 'QTN-',
        'next_number' => 1,
        'padding' => 5,
        'active' => true,
    ]);

    $service = app(
        DocumentNumberService::class
    );

    expect($service->next('quotation'))
        ->toBe('QTN-00001');

    expect($service->next('quotation'))
        ->toBe('QTN-00002');

    expect(
        DocumentSequence::where(
            'type',
            'quotation'
        )->value('next_number')
    )->toBe(3);
});
