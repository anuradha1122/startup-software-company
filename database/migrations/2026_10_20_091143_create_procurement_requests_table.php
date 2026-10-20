<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_requests', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('request_number', 80)->unique();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title');
            $table->text('business_reason');

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent',
            ])->default('medium');

            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected',
                'cancelled',
                'ordered',
                'partially_received',
                'received',
            ])->default('draft');

            $table->char('currency', 3);

            $table->decimal(
                'estimated_total',
                15,
                2
            )->default(0);

            $table->foreignId('requested_by')
                ->constrained('users')
                ->restrictOnDelete();

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

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'status',
                'priority',
            ]);

            $table->index([
                'project_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'procurement_requests'
        );
    }
};
