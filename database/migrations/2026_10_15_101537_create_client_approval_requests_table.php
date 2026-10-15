<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_approval_requests', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string(
                'approval_number',
                80
            )->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'requirements',
                'proposal',
                'quotation',
                'contract',
                'milestone',
                'deliverable',
                'document',
                'other',
            ]);

            $table->string('title');

            /*
             * This must contain client-safe wording only.
             * Never dump internal model JSON here.
             */
            $table->text('summary')
                ->nullable();

            /*
             * Internal traceability only.
             * These fields are never resolved automatically
             * and serialized to the portal.
             */
            $table->string('source_type', 100)
                ->nullable();

            $table->unsignedBigInteger('source_id')
                ->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled',
            ])->default('pending');

            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('requested_at');

            $table->timestamp('due_at')
                ->nullable();

            $table->foreignId('responded_by')
                ->nullable()
                ->constrained('client_portal_users')
                ->nullOnDelete();

            $table->text('response_comment')
                ->nullable();

            $table->timestamp('responded_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['client_id', 'status'],
                'client_approval_status_idx'
            );

            $table->index(
                ['project_id', 'status'],
                'project_client_approval_idx'
            );

            $table->index(
                ['source_type', 'source_id'],
                'client_approval_source_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'client_approval_requests'
        );
    }
};
