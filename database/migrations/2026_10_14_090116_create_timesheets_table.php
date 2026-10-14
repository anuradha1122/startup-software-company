<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timesheets', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('week_start');
            $table->date('week_end');

            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected',
            ])->default('draft');

            $table->text('notes')
                ->nullable();

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

            $table->unique(
                [
                    'user_id',
                    'week_start',
                ],
                'user_timesheet_week_unique'
            );

            $table->index(
                [
                    'status',
                    'week_start',
                ],
                'timesheet_status_week_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
