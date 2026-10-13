<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_task_dependencies', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('project_task_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('depends_on_task_id')
                ->constrained('project_tasks')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                [
                    'project_task_id',
                    'depends_on_task_id',
                ],
                'task_dependency_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_task_dependencies'
        );
    }
};
