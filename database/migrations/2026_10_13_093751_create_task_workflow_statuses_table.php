<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_workflow_statuses', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('name', 100);
            $table->string('slug', 100)->unique();

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_initial')->default(false);
            $table->boolean('is_in_progress')->default(false);
            $table->boolean('is_review')->default(false);
            $table->boolean('is_completed')->default(false);

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index([
                'active',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_workflow_statuses');
    }
};
