<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenances', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('asset_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('type', [
                'inspection',
                'repair',
                'service',
                'upgrade',
            ]);

            $table->enum('status', [
                'scheduled',
                'in_progress',
                'completed',
                'cancelled',
            ])->default('scheduled');

            $table->date(
                'started_date'
            )->nullable();

            $table->date(
                'completed_date'
            )->nullable();

            $table->string('vendor')
                ->nullable();

            $table->char(
                'currency',
                3
            )->nullable();

            $table->decimal(
                'cost',
                15,
                2
            )->nullable();

            $table->text(
                'description'
            );

            $table->text(
                'resolution'
            )->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'asset_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'asset_maintenances'
        );
    }
};
