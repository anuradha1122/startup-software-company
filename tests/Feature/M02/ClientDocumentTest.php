<?php

use App\Models\Client;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('local');

    $this->seed(
        RolePermissionSeeder::class
    );
});

it('stores client documents privately', function (): void {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $client = Client::create([
        'client_number' => 'CLI-DOC01',
        'name' => 'Document Client',
        'status' => 'active',
        'created_by' => $sales->id,
    ]);

    $file = UploadedFile::fake()
        ->create(
            'registration.pdf',
            100,
            'application/pdf'
        );

    $this
        ->actingAs($sales)
        ->post(
            "/admin/clients/{$client->public_id}/documents",
            [
                'title' => 'Company Registration',
                'category' => 'registration',
                'visibility' => 'internal',
                'file' => $file,
            ]
        )
        ->assertRedirect();

    $document = $client
        ->documents()
        ->firstOrFail();

    expect(
        $document->disk
    )->toBe('local');

    Storage::disk('local')
        ->assertExists(
            $document->path
        );
});

it('authorised users can securely download a document', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $client = Client::create([
        'client_number' => 'CLI-DOC02',
        'name' => 'Document Client',
        'status' => 'active',
        'created_by' => $admin->id,
    ]);

    Storage::disk('local')->put(
        "clients/{$client->public_id}/test.pdf",
        'test-content'
    );

    $document = $client
        ->documents()
        ->create([
            'category' => 'other',
            'title' => 'Test',
            'original_name' => 'test.pdf',
            'disk' => 'local',
            'path' => "clients/{$client->public_id}/test.pdf",
            'mime_type' => 'application/pdf',
            'size' => 12,
            'visibility' => 'internal',
            'uploaded_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/clients/{$client->public_id}/documents/{$document->public_id}/download"
        )
        ->assertOk();
});
