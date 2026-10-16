<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_entries', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('payroll_period_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_profile_id')
                ->constrained()
                ->restrictOnDelete();

            $table->char('currency', 3);

            /*
             * Payroll snapshots.
             * Historical payslips must not change if compensation changes later.
             */
            $table->decimal('basic_salary', 15, 2)
                ->default(0);

            $table->decimal('allowances', 15, 2)
                ->default(0);

            $table->decimal('overtime_amount', 15, 2)
                ->default(0);

            $table->decimal('gross_pay', 15, 2)
                ->default(0);

            $table->decimal('deductions', 15, 2)
                ->default(0);

            $table->decimal('net_pay', 15, 2)
                ->default(0);

            $table->decimal('attendance_days', 8, 2)
                ->default(0);

            $table->decimal('leave_days', 8, 2)
                ->default(0);

            $table->decimal('unpaid_leave_days', 8, 2)
                ->default(0);

            $table->timestamp('calculated_at')
                ->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'payroll_period_id',
                    'employee_profile_id',
                ],
                'payroll_period_employee_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_entries');
    }
};
