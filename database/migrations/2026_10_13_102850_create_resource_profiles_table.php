<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_profiles', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('job_title', 150)
                ->nullable();

            $table->string('department', 150)
                ->nullable();

            $table->unsignedSmallInteger(
                'capacity_hours_per_week'
            )->default(40);

            $table->unsignedTinyInteger(
                'default_allocation_percent'
            )->default(100);

            $table->enum('seniority', [
                'trainee',
                'junior',
                'mid',
                'senior',
                'lead',
                'principal',
            ])->nullable();

            $table->text('bio')
                ->nullable();

            $table->boolean(
                'available_for_projects'
            )->default(true);

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'available_for_projects',
                'department',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'resource_profiles'
        );
    }
};
