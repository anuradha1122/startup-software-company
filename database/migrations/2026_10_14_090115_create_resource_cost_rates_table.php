<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_cost_rates', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->char('currency', 3)
                ->default('LKR');

            $table->decimal(
                'hourly_cost_rate',
                15,
                2
            );

            $table->date('effective_from');

            $table->date('effective_to')
                ->nullable();

            $table->boolean('active')
                ->default(true);

            $table->text('notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                [
                    'user_id',
                    'effective_from',
                ],
                'resource_cost_rate_start_unique'
            );

            $table->index(
                [
                    'user_id',
                    'active',
                ],
                'resource_cost_rate_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'resource_cost_rates'
        );
    }
};
