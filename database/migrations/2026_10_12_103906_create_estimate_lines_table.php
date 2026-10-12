<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimate_lines', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('estimate_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->string('module_name');

            $table->text('description')
                ->nullable();

            $table->decimal(
                'estimated_hours',
                10,
                2
            );

            $table->decimal(
                'billing_rate',
                12,
                2
            );

            $table->decimal(
                'cost_rate',
                12,
                2
            )->default(0);

            $table->decimal(
                'revenue_amount',
                15,
                2
            )->default(0);

            $table->decimal(
                'cost_amount',
                15,
                2
            )->default(0);

            $table->timestamps();

            $table->index([
                'estimate_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'estimate_lines'
        );
    }
};
