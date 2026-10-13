<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_task_comments', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_task_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->longText('body');

            $table->boolean('client_visible')
                ->default(false);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'project_task_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_task_comments'
        );
    }
};
