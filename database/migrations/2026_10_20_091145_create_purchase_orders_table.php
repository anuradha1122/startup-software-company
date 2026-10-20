<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string(
                'purchase_order_number',
                80
            )->unique();

            $table->foreignId(
                'procurement_request_id'
            )
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('supplier_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->char('currency', 3);

            $table->date('order_date');

            $table->date(
                'expected_delivery_date'
            )->nullable();

            $table->enum('status', [
                'draft',
                'issued',
                'partially_received',
                'received',
                'cancelled',
            ])->default('draft');

            $table->decimal(
                'subtotal',
                15,
                2
            )->default(0);

            $table->decimal(
                'tax_amount',
                15,
                2
            )->default(0);

            $table->decimal(
                'total',
                15,
                2
            )->default(0);

            $table->string(
                'supplier_reference'
            )->nullable();

            $table->text('notes')
                ->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('issued_at')
                ->nullable();

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'supplier_id',
                'status',
            ]);

            $table->index([
                'project_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'purchase_orders'
        );
    }
};
