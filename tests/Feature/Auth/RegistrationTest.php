<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration screen is disabled', function () {
    $response = $this->get('/register');

    $response->assertNotFound();
});

test('users cannot register publicly', function () {
    $response = $this->post('/register', [
        'name' => 'Public User',
        'email' => 'public@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertNotFound();

    $this->assertDatabaseMissing('users', [
        'email' => 'public@example.com',
    ]);
});