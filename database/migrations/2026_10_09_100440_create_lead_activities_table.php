<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_activities', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'call',
                'email',
                'meeting',
                'note',
                'follow_up',
            ]);

            $table->string('subject');
            $table->text('description')->nullable();

            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'lead_id',
                'type',
            ]);

            $table->index('due_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};
