<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('lead_number', 30)->unique();

            $table->string('title');
            $table->string('company_name')->nullable();

            $table->foreignId('stage_id')
                ->constrained('lead_stages')
                ->restrictOnDelete();

            $table->foreignId('source_id')
                ->nullable()
                ->constrained('lead_sources')
                ->nullOnDelete();

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('estimated_value', 15, 2)
                ->nullable();

            $table->char('currency', 3)->default('LKR');

            $table->unsignedTinyInteger('probability')
                ->default(0);

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent',
            ])->default('medium');

            $table->enum('status', [
                'open',
                'won',
                'lost',
                'converted',
            ])->default('open');

            $table->timestamp('next_follow_up_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            $table->timestamp('follow_up_reminded_at')->nullable();
            $table->timestamp('stale_notified_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('converted_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'stage_id',
                'status',
            ]);

            $table->index('next_follow_up_at');
            $table->index('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
