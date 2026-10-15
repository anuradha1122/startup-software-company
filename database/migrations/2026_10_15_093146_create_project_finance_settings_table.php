<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_finance_settings', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal(
                'target_margin_percent',
                8,
                2
            )->default(30);

            $table->decimal(
                'warning_margin_percent',
                8,
                2
            )->default(20);

            $table->boolean('alerts_enabled')
                ->default(true);

            $table->timestamp(
                'last_margin_alerted_at'
            )->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                'project_id',
                'project_finance_setting_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_finance_settings'
        );
    }
};
