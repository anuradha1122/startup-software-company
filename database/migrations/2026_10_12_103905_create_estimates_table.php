<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimates', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('estimate_number', 30)
                ->unique();

            $table->foreignId('requirement_version_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('status', [
                'draft',
                'approved',
                'superseded',
            ])->default('draft');

            $table->char('currency', 3)
                ->default('LKR');

            $table->decimal(
                'contingency_percent',
                5,
                2
            )->default(0);

            $table->decimal(
                'total_hours',
                12,
                2
            )->default(0);

            $table->decimal(
                'estimated_revenue',
                15,
                2
            )->default(0);

            $table->decimal(
                'estimated_cost',
                15,
                2
            )->default(0);

            $table->decimal(
                'gross_margin',
                15,
                2
            )->default(0);

            $table->decimal(
                'gross_margin_percent',
                8,
                2
            )->default(0);

            $table->text('assumptions')
                ->nullable();

            $table->text('internal_notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'requirement_version_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
