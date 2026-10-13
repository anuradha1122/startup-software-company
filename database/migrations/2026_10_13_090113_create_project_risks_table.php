<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_risks', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('risk_number', 50);

            $table->string('title');

            $table->text('description')
                ->nullable();

            $table->string('category', 100)
                ->nullable();

            $table->unsignedTinyInteger('likelihood');

            $table->unsignedTinyInteger('impact');

            $table->unsignedTinyInteger('score');

            $table->enum('status', [
                'open',
                'mitigating',
                'accepted',
                'closed',
            ])->default('open');

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('mitigation')
                ->nullable();

            $table->text('contingency')
                ->nullable();

            $table->date('review_date')
                ->nullable();

            $table->timestamp('escalated_at')
                ->nullable();

            $table->timestamp('closed_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'project_id',
                'risk_number',
            ]);

            $table->index([
                'project_id',
                'status',
            ]);

            $table->index('score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_risks'
        );
    }
};
