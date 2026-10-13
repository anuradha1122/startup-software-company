<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('project_number', 30)->unique();

            $table->foreignId('contract_id')
                ->unique()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('requirement_version_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('estimate_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name');

            $table->enum('status', [
                'planned',
                'active',
                'on_hold',
                'completed',
                'cancelled',
            ])->default('planned');

            $table->enum('health', [
                'green',
                'amber',
                'red',
            ])->default('green');

            $table->string('methodology', 100)
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->char('currency', 3);

            $table->decimal(
                'contract_value',
                15,
                2
            );

            $table->foreignId('project_manager_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('client_sponsor_contact_id')
                ->nullable()
                ->constrained('client_contacts')
                ->nullOnDelete();

            $table->date('planned_start_date')
                ->nullable();

            $table->date('planned_end_date')
                ->nullable();

            $table->date('actual_start_date')
                ->nullable();

            $table->date('actual_end_date')
                ->nullable();

            $table->timestamp('activated_at')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'client_id',
                'status',
            ]);

            $table->index([
                'project_manager_id',
                'status',
            ]);

            $table->index('health');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
