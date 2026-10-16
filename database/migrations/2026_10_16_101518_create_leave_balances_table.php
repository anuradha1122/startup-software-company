<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('employee_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('leave_type_id')
                ->constrained()
                ->restrictOnDelete();

            $table->unsignedSmallInteger('year');

            $table->decimal('opening_balance', 8, 2)
                ->default(0);

            $table->decimal('accrued', 8, 2)
                ->default(0);

            $table->decimal('used', 8, 2)
                ->default(0);

            $table->decimal('adjusted', 8, 2)
                ->default(0);

            $table->decimal('available', 8, 2)
                ->default(0);

            $table->timestamps();

            $table->unique(
                [
                    'employee_profile_id',
                    'leave_type_id',
                    'year',
                ],
                'employee_leave_balance_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
