<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string(
                'receipt_number',
                80
            )->unique();

            $table->foreignId(
                'purchase_order_id'
            )
                ->constrained()
                ->restrictOnDelete();

            $table->date('received_date');

            $table->string(
                'delivery_reference'
            )->nullable();

            $table->text('notes')
                ->nullable();

            $table->foreignId('received_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index([
                'purchase_order_id',
                'received_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'goods_receipts'
        );
    }
};
