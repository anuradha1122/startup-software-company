<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('payment_number', 80)
                ->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('payment_date');

            $table->char('currency', 3);

            $table->decimal('amount', 15, 2);

            $table->decimal('allocated_amount', 15, 2)
                ->default(0);

            $table->decimal('unallocated_amount', 15, 2)
                ->default(0);

            $table->enum('method', [
                'bank_transfer',
                'cash',
                'card',
                'cheque',
                'online',
                'other',
            ]);

            $table->string('reference_number', 150)
                ->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('receipt_sent_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['client_id', 'payment_date'],
                'payment_client_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
