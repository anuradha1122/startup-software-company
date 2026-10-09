<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not authenticate a suspended user', function (): void {
    $user = User::factory()->suspended()->create([
        'email' => 'suspended@example.com',
        'password' => 'Password123!',
    ]);

    $this->post('/login', [
        'email' => 'suspended@example.com',
        'password' => 'Password123!',
    ]);

    $this->assertGuest();
});

it('logs out a user whose account becomes inactive', function (): void {
    $user = User::factory()->inactive()->create();

    $this
        ->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect('/login');

    $this->assertGuest();
});