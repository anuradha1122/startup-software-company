<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_adjustments', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('payroll_entry_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'allowance',
                'deduction',
            ]);

            $table->string('label');

            $table->decimal('amount', 15, 2);

            $table->text('notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'payroll_adjustments'
        );
    }
};
