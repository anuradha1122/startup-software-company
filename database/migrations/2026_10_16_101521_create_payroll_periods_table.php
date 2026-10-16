<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('payroll_number', 80)->unique();

            $table->string('name');

            $table->date('period_start');
            $table->date('period_end');
            $table->date('payment_date');

            $table->enum('status', [
                'draft',
                'calculated',
                'approved',
                'published',
                'closed',
            ])->default('draft');

            $table->timestamp('calculated_at')
                ->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

            $table->foreignId('published_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('published_at')
                ->nullable();

            $table->timestamp('closed_at')
                ->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'period_start',
                    'period_end',
                ],
                'payroll_period_dates_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
