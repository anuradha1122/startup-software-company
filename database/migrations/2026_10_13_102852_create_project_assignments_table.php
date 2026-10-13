<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_assignments', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('role', 150);

            $table->unsignedTinyInteger(
                'allocation_percent'
            )->default(100);

            $table->enum('status', [
                'planned',
                'active',
                'ended',
                'cancelled',
            ])->default('planned');

            $table->date('start_date')
                ->nullable();

            $table->date('end_date')
                ->nullable();

            $table->boolean('billable')
                ->default(true);

            $table->text('responsibilities')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('ended_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('ended_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                [
                    'project_id',
                    'user_id',
                ],
                'project_user_assignment_unique'
            );

            $table->index(
                [
                    'project_id',
                    'status',
                ],
                'project_assignment_status_idx'
            );

            $table->index(
                [
                    'user_id',
                    'status',
                ],
                'user_assignment_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_assignments'
        );
    }
};
