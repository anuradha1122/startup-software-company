<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('employee_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('work_date');

            $table->dateTime('clock_in_at')
                ->nullable();

            $table->dateTime('clock_out_at')
                ->nullable();

            $table->decimal('worked_hours', 8, 2)
                ->default(0);

            $table->decimal('overtime_hours', 8, 2)
                ->default(0);

            $table->enum('status', [
                'present',
                'absent',
                'leave',
                'holiday',
                'weekend',
                'incomplete',
            ])->default('present');

            $table->enum('source', [
                'self',
                'admin',
                'system',
            ])->default('self');

            $table->text('notes')
                ->nullable();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                [
                    'employee_profile_id',
                    'work_date',
                ],
                'employee_attendance_date_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'attendance_records'
        );
    }
};
