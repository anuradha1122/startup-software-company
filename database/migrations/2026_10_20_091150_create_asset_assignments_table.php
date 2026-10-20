<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_assignments', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('asset_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('assigned_date');

            $table->date(
                'expected_return_date'
            )->nullable();

            $table->date(
                'returned_date'
            )->nullable();

            $table->enum('status', [
                'active',
                'returned',
            ])->default('active');

            $table->text(
                'assignment_notes'
            )->nullable();

            $table->text(
                'return_notes'
            )->nullable();

            $table->foreignId('assigned_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('returned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'asset_id',
                'status',
            ]);

            $table->index([
                'user_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'asset_assignments'
        );
    }
};
