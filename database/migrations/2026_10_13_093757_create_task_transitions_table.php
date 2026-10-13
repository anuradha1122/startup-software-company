<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_transitions', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('project_task_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('from_status_id')
                ->nullable()
                ->constrained('task_workflow_statuses')
                ->nullOnDelete();

            $table->foreignId('to_status_id')
                ->constrained('task_workflow_statuses')
                ->restrictOnDelete();

            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')
                ->nullable();

            $table->timestamp('changed_at');

            $table->timestamps();

            $table->index([
                'project_task_id',
                'changed_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'task_transitions'
        );
    }
};
