<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_expenses', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('expense_number', 80)->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('type', [
                'travel',
                'software',
                'hosting',
                'equipment',
                'supplier',
                'professional_service',
                'communication',
                'other',
            ]);

            $table->string('title');
            $table->text('description')->nullable();

            $table->date('expense_date');

            $table->string('reference_number', 150)
                ->nullable();

            $table->char('currency', 3)
                ->default('LKR');

            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)
                ->default(0);

            $table->decimal('total_amount', 15, 2);

            $table->boolean('billable_to_client')
                ->default(false);

            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected',
                'cancelled',
            ])->default('draft');

            $table->foreignId('incurred_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('submitted_at')
                ->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('review_notes')
                ->nullable();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['project_id', 'status'],
                'project_expense_status_idx'
            );

            $table->index(
                ['supplier_id', 'status'],
                'supplier_expense_status_idx'
            );

            $table->index(
                ['expense_date', 'status'],
                'expense_date_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_expenses'
        );
    }
};
