<?php

use App\Models\Timesheet;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('creates one weekly timesheet per user', function (): void {
    $user = User::factory()->create();

    $user->assignRole(
        'Project Manager'
    );

    $weekStart = now()
        ->startOfWeek()
        ->format('Y-m-d');

    $this
        ->actingAs($user)
        ->post(
            '/admin/timesheets',
            [
                'week_start' => $weekStart,
                'notes' => 'Weekly work.',
            ]
        )
        ->assertRedirect();

    $this
        ->actingAs($user)
        ->post(
            '/admin/timesheets',
            [
                'week_start' => $weekStart,
                'notes' => 'Duplicate attempt.',
            ]
        )
        ->assertRedirect();

    expect(
        Timesheet::where(
            'user_id',
            $user->id
        )->count()
    )->toBe(1);

    $timesheet = Timesheet::firstOrFail();

    expect(
        $timesheet->status
    )->toBe('draft');

    expect(
        $timesheet->week_start
            ->format('Y-m-d')
    )->toBe($weekStart);

    expect(
        $timesheet->week_end
            ->format('Y-m-d')
    )->toBe(
        now()
            ->startOfWeek()
            ->endOfWeek()
            ->format('Y-m-d')
    );
});
