<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_submissions', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_task_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('version_number');

            $table->enum('status', [
                'submitted',
                'changes_required',
                'approved',
                'superseded',
            ])->default('submitted');

            $table->longText('summary');

            $table->text('testing_notes')
                ->nullable();

            $table->text('deployment_notes')
                ->nullable();

            $table->string('reference_url', 1000)
                ->nullable();

            $table->foreignId('submitted_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp('submitted_at');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('review_notes')
                ->nullable();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'project_task_id',
                'version_number',
            ]);

            $table->index([
                'project_task_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'work_submissions'
        );
    }
};
