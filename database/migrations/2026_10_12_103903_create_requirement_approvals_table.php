<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requirement_approvals', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('requirement_version_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('client_contact_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('approver_name');
            $table->string('approver_email');

            $table->enum('status', [
                'pending',
                'approved',
                'revision_required',
            ])->default('pending');

            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('requested_at');

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('decision_notes')
                ->nullable();

            $table->timestamp('responded_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'requirement_version_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'requirement_approvals'
        );
    }
};
