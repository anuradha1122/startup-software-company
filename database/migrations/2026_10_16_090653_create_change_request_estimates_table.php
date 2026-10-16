<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_request_estimates', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('change_request_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->char('currency', 3);

            $table->decimal(
                'client_amount',
                15,
                2
            )->default(0);

            /*
             * Internal costing.
             * Never expose through portal DTOs/PDFs.
             */
            $table->decimal(
                'internal_estimated_cost',
                15,
                2
            )->default(0);

            $table->decimal(
                'estimated_hours',
                10,
                2
            )->default(0);

            $table->unsignedInteger(
                'estimated_days'
            )->default(0);

            $table->text('client_notes')
                ->nullable();

            $table->text('internal_notes')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                'change_request_id',
                'change_request_estimate_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'change_request_estimates'
        );
    }
};
