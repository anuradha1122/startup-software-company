<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('task_sequence');

            $table->string('task_number', 80);

            $table->foreignId('parent_task_id')
                ->nullable()
                ->constrained('project_tasks')
                ->nullOnDelete();

            $table->foreignId('project_phase_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('project_milestone_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('workflow_status_id')
                ->constrained('task_workflow_statuses')
                ->restrictOnDelete();

            $table->string('title');

            $table->longText('description')
                ->nullable();

            $table->enum('type', [
                'task',
                'bug',
                'change',
                'research',
            ])->default('task');

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent',
            ])->default('medium');

            $table->foreignId('assignee_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal(
                'estimated_hours',
                10,
                2
            )->nullable();

            $table->date('planned_start_date')
                ->nullable();

            $table->date('due_date')
                ->nullable();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('client_visible')
                ->default(false);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'project_id',
                'task_sequence',
            ]);

            $table->unique('task_number');

            $table->index([
                'project_id',
                'workflow_status_id',
            ]);

            $table->index([
                'project_id',
                'assignee_id',
            ]);

            $table->index([
                'project_id',
                'priority',
            ]);

            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
