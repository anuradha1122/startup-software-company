<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_submission_attachments', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('work_submission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('original_name');

            $table->string('disk', 50)
                ->default('local');

            $table->string('path');

            $table->string('mime_type', 150)
                ->nullable();

            $table->unsignedBigInteger('size')
                ->default(0);

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                'work_submission_id'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'work_submission_attachments'
        );
    }
};
