<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('invoice_number', 80)->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('billing_contact_id')
                ->nullable()
                ->constrained('client_contacts')
                ->nullOnDelete();

            $table->enum('status', [
                'draft',
                'issued',
                'partially_paid',
                'paid',
                'overdue',
                'void',
            ])->default('draft');

            $table->date('invoice_date');
            $table->date('due_date');

            $table->char('currency', 3);

            $table->string('billing_name');
            $table->string('billing_email')->nullable();
            $table->text('billing_address')->nullable();

            $table->text('notes')->nullable();
            $table->text('payment_instructions')->nullable();

            $table->decimal('subtotal', 15, 2)
                ->default(0);

            $table->decimal('tax_amount', 15, 2)
                ->default(0);

            $table->decimal('total_amount', 15, 2)
                ->default(0);

            $table->decimal('amount_paid', 15, 2)
                ->default(0);

            $table->decimal('balance_due', 15, 2)
                ->default(0);

            $table->timestamp('issued_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('voided_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('voided_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['client_id', 'status'],
                'invoice_client_status_idx'
            );

            $table->index(
                ['project_id', 'status'],
                'invoice_project_status_idx'
            );

            $table->index(
                ['status', 'due_date'],
                'invoice_status_due_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
