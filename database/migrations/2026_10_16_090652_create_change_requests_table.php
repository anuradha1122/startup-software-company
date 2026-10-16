<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_requests', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string(
                'change_request_number',
                80
            )->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('client_portal_user_id')
                ->nullable()
                ->constrained('client_portal_users')
                ->nullOnDelete();

            $table->string('title');

            $table->text('description');

            $table->text('business_reason')
                ->nullable();

            /*
             * Client-safe impact statement.
             */
            $table->text('impact_summary')
                ->nullable();

            /*
             * Internal analysis must never be serialized to portal.
             */
            $table->text('internal_analysis')
                ->nullable();

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent',
            ])->default('medium');

            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'awaiting_client',
                'approved',
                'rejected',
                'cancelled',
                'implemented',
            ])->default('draft');

            $table->timestamp('submitted_at')
                ->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('review_notes')
                ->nullable();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamp('rejected_at')
                ->nullable();

            $table->timestamp('implemented_at')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['project_id', 'status'],
                'change_request_project_status_idx'
            );

            $table->index(
                ['client_id', 'status'],
                'change_request_client_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'change_requests'
        );
    }
};
