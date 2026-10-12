<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requirement_versions', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('requirement_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('version_number');

            $table->enum('status', [
                'draft',
                'in_review',
                'pending_client_approval',
                'approved',
                'revision_required',
                'superseded',
            ])->default('draft');

            $table->string('version_title')
                ->nullable();

            $table->text('objective')
                ->nullable();

            $table->longText('scope_in')
                ->nullable();

            $table->longText('scope_out')
                ->nullable();

            $table->json('functional_requirements')
                ->nullable();

            $table->json('non_functional_requirements')
                ->nullable();

            $table->json('assumptions')
                ->nullable();

            $table->json('constraints')
                ->nullable();

            $table->json('acceptance_criteria')
                ->nullable();

            $table->longText('client_notes')
                ->nullable();

            $table->longText('internal_notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('submitted_for_review_at')
                ->nullable();

            $table->timestamp('submitted_for_approval_at')
                ->nullable();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'requirement_id',
                'version_number',
            ]);

            $table->index([
                'requirement_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'requirement_versions'
        );
    }
};
