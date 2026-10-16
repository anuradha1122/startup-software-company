<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_compensations', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('employee_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->char('currency', 3);

            $table->decimal('monthly_basic_salary', 15, 2)
                ->default(0);

            $table->decimal('fixed_allowance', 15, 2)
                ->default(0);

            $table->decimal('fixed_deduction', 15, 2)
                ->default(0);

            $table->date('effective_from');

            $table->date('effective_to')
                ->nullable();

            $table->boolean('active')
                ->default(true);

            $table->text('notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                [
                    'employee_profile_id',
                    'active',
                ],
                'employee_comp_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'employee_compensations'
        );
    }
};
