<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_request_items', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId(
                'procurement_request_id'
            )
                ->constrained()
                ->cascadeOnDelete();

            $table->string('description');

            $table->decimal(
                'quantity',
                12,
                2
            );

            $table->string(
                'unit',
                50
            )->default('unit');

            $table->decimal(
                'estimated_unit_cost',
                15,
                2
            )->default(0);

            $table->decimal(
                'estimated_total',
                15,
                2
            )->default(0);

            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'procurement_request_items'
        );
    }
};
