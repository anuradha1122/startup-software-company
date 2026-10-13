<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_milestones', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('project_phase_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');

            $table->text('description')
                ->nullable();

            $table->date('due_date');

            $table->enum('status', [
                'planned',
                'in_progress',
                'completed',
                'delayed',
                'cancelled',
            ])->default('planned');

            $table->boolean('client_visible')
                ->default(true);

            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'project_id',
                'status',
            ]);

            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_milestones'
        );
    }
};
