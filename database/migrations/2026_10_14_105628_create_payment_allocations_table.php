<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_allocations', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('payment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('invoice_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('amount', 15, 2);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['payment_id', 'invoice_id'],
                'payment_invoice_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'payment_allocations'
        );
    }
};
