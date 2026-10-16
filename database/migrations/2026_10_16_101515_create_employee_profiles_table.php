<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_profiles', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('employee_number', 80)->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('employment_type', [
                'permanent',
                'probation',
                'contract',
                'intern',
                'part_time',
            ]);

            $table->enum('status', [
                'active',
                'on_leave',
                'suspended',
                'terminated',
            ])->default('active');

            $table->string('department')
                ->nullable();

            $table->string('job_title')
                ->nullable();

            $table->date('hire_date');

            $table->date('probation_end_date')
                ->nullable();

            $table->date('termination_date')
                ->nullable();

            $table->string('work_location')
                ->nullable();

            $table->string('emergency_contact_name')
                ->nullable();

            $table->string('emergency_contact_phone', 30)
                ->nullable();

            $table->text('hr_notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                'user_id',
                'employee_profile_user_unique'
            );

            $table->index(
                ['status', 'employment_type'],
                'employee_status_type_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
