<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('name');

            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->foreignId('source_lead_id')
                ->nullable()
                ->unique()
                ->constrained('leads')
                ->nullOnDelete();

            $table->foreignId('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
