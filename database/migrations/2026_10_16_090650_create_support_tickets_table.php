<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('ticket_number', 80)->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('client_portal_user_id')
                ->nullable()
                ->constrained('client_portal_users')
                ->nullOnDelete();

            $table->string('subject');

            $table->text('description');

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent',
            ])->default('medium');

            $table->enum('severity', [
                'minor',
                'normal',
                'major',
                'critical',
            ])->default('normal');

            $table->enum('status', [
                'open',
                'assigned',
                'in_progress',
                'waiting_client',
                'resolved',
                'closed',
                'cancelled',
            ])->default('open');

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('first_response_due_at')
                ->nullable();

            $table->timestamp('resolution_due_at')
                ->nullable();

            $table->timestamp('first_responded_at')
                ->nullable();

            $table->timestamp('resolved_at')
                ->nullable();

            $table->timestamp('closed_at')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['client_id', 'status'],
                'support_ticket_client_status_idx'
            );

            $table->index(
                ['assigned_to', 'status'],
                'support_ticket_assignee_status_idx'
            );

            $table->index(
                ['status', 'first_response_due_at'],
                'support_ticket_response_due_idx'
            );

            $table->index(
                ['status', 'resolution_due_at'],
                'support_ticket_resolution_due_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
