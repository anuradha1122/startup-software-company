<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('proposal_number', 30)->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('requirement_version_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('estimate_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('title');

            $table->enum('status', [
                'draft',
                'approved',
                'sent',
                'accepted',
                'rejected',
                'expired',
                'superseded',
            ])->default('draft');

            $table->text('executive_summary')->nullable();
            $table->text('solution_overview')->nullable();
            $table->text('delivery_approach')->nullable();
            $table->text('commercial_notes')->nullable();

            $table->date('valid_until')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->text('decision_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'client_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
