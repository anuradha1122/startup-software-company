<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_stakeholders', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'internal',
                'client',
            ]);

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('client_contact_id')
                ->nullable()
                ->constrained('client_contacts')
                ->nullOnDelete();

            $table->string('name');
            $table->string('email')->nullable();

            $table->string('role', 150);

            $table->text('responsibility')
                ->nullable();

            $table->boolean('is_primary')
                ->default(false);

            $table->boolean('receive_notifications')
                ->default(true);

            $table->boolean('active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'project_id',
                'type',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_stakeholders'
        );
    }
};
