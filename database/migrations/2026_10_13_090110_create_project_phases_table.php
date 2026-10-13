<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_phases', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->string('name');

            $table->text('description')
                ->nullable();

            $table->enum('status', [
                'planned',
                'active',
                'completed',
                'on_hold',
                'cancelled',
            ])->default('planned');

            $table->date('planned_start_date')
                ->nullable();

            $table->date('planned_end_date')
                ->nullable();

            $table->date('actual_start_date')
                ->nullable();

            $table->date('actual_end_date')
                ->nullable();

            $table->timestamps();

            $table->index([
                'project_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_phases'
        );
    }
};
