<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipt_items', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId(
                'goods_receipt_id'
            )
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId(
                'purchase_order_item_id'
            )
                ->constrained()
                ->restrictOnDelete();

            $table->decimal(
                'quantity_received',
                12,
                2
            );

            $table->decimal(
                'quantity_rejected',
                12,
                2
            )->default(0);

            $table->text(
                'condition_notes'
            )->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'goods_receipt_items'
        );
    }
};
