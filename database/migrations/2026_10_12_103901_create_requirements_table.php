<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requirements', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('requirement_number', 30)
                ->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('title');

            $table->enum('status', [
                'draft',
                'in_review',
                'pending_client_approval',
                'approved',
                'revision_required',
                'archived',
            ])->default('draft');

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'client_id',
                'status',
            ]);

            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
