<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_runs', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('report_type', 100);

            $table->enum('format', [
                'excel',
                'pdf',
            ]);

            $table->enum('status', [
                'completed',
                'failed',
            ])->default('completed');

            $table->json('filters')
                ->nullable();

            $table->string('download_name')
                ->nullable();

            $table->unsignedInteger('row_count')
                ->nullable();

            $table->text('failure_message')
                ->nullable();

            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('generated_at');

            $table->timestamps();

            $table->index(
                [
                    'report_type',
                    'generated_at',
                ],
                'report_run_type_date_idx'
            );

            $table->index(
                [
                    'generated_by',
                    'generated_at',
                ],
                'report_run_user_date_idx'
            );

            $table->index(
                [
                    'status',
                    'generated_at',
                ],
                'report_run_status_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'report_runs'
        );
    }
};
