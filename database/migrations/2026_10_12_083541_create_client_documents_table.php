<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_documents', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('category', 50);

            $table->string('title');

            $table->string('original_name');

            $table->string('disk', 50)
                ->default('local');

            $table->string('path');

            $table->string('mime_type', 150)
                ->nullable();

            $table->unsignedBigInteger('size')
                ->default(0);

            $table->enum('visibility', [
                'internal',
                'client',
            ])->default('internal');

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'client_id',
                'category',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'client_documents'
        );
    }
};
