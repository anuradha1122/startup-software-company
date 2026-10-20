<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId(
                'purchase_order_id'
            )
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId(
                'procurement_request_item_id'
            )
                ->nullable()
                ->constrained()
                ->nullOnDelete();

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
                'unit_cost',
                15,
                2
            );

            $table->decimal(
                'line_total',
                15,
                2
            );

            $table->decimal(
                'received_quantity',
                12,
                2
            )->default(0);

            $table->boolean(
                'asset_trackable'
            )->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'purchase_order_items'
        );
    }
};
