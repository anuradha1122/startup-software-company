<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('quotation_number', 30)->unique();

            $table->foreignId('proposal_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('requirement_version_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('estimate_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('status', [
                'draft',
                'approved',
                'sent',
                'accepted',
                'rejected',
                'expired',
                'superseded',
            ])->default('draft');

            $table->char('currency', 3)->default('LKR');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->unsignedSmallInteger('payment_terms_days')
                ->default(30);

            $table->text('payment_schedule')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();

            $table->date('valid_until');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->text('decision_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'client_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
