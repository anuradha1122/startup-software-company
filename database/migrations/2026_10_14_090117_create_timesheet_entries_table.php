<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timesheet_entries', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('timesheet_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('project_task_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('project_assignment_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('work_date');

            $table->decimal(
                'hours',
                5,
                2
            );

            $table->text('description')
                ->nullable();

            $table->boolean('billable')
                ->default(true);

            $table->decimal(
                'hourly_cost_rate_snapshot',
                15,
                2
            )->nullable();

            $table->decimal(
                'internal_cost_amount',
                15,
                2
            )->nullable();

            $table->char(
                'cost_currency',
                3
            )->nullable();

            $table->timestamp('costed_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                [
                    'timesheet_id',
                    'work_date',
                ],
                'timesheet_entry_date_idx'
            );

            $table->index(
                [
                    'project_id',
                    'work_date',
                ],
                'project_time_date_idx'
            );

            $table->index(
                'project_task_id',
                'timesheet_task_idx'
            );

            $table->index(
                'project_assignment_id',
                'timesheet_assignment_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'timesheet_entries'
        );
    }
};
