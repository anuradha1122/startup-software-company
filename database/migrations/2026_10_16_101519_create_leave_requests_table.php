<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('leave_number', 80)->unique();

            $table->foreignId('employee_profile_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('leave_type_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->decimal('days', 8, 2);

            $table->text('reason')
                ->nullable();

            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected',
                'cancelled',
            ])->default('draft');

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

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamp('rejected_at')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                [
                    'employee_profile_id',
                    'status',
                ],
                'leave_request_employee_status_idx'
            );

            $table->index(
                [
                    'start_date',
                    'end_date',
                ],
                'leave_request_dates_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
