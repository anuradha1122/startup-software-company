<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_charters', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('version_number');

            $table->enum('status', [
                'draft',
                'approved',
                'superseded',
            ])->default('draft');

            $table->text('business_objective')
                ->nullable();

            $table->text('project_objective')
                ->nullable();

            $table->text('governance_structure')
                ->nullable();

            $table->text('communication_plan')
                ->nullable();

            $table->text('escalation_process')
                ->nullable();

            $table->json('success_criteria')
                ->nullable();

            $table->json('assumptions')
                ->nullable();

            $table->json('constraints')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'project_id',
                'version_number',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_charters'
        );
    }
};
