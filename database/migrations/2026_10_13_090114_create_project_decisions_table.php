<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_decisions', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('decision');

            $table->text('rationale')
                ->nullable();

            $table->foreignId('decided_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('decided_at');

            $table->boolean('client_visible')
                ->default(false);

            $table->timestamps();

            $table->index([
                'project_id',
                'decided_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_decisions'
        );
    }
};
