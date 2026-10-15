<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_portal_documents', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('category', [
                'requirements',
                'proposal',
                'quotation',
                'contract',
                'project',
                'deliverable',
                'invoice_support',
                'other',
            ])->default('other');

            $table->string('title');

            $table->text('description')
                ->nullable();

            $table->string('original_name');

            $table->string('disk', 50)
                ->default('local');

            $table->string('path');

            $table->string('mime_type', 150)
                ->nullable();

            $table->unsignedBigInteger('size')
                ->default(0);

            $table->timestamp('published_at');

            $table->timestamp('expires_at')
                ->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['client_id', 'published_at'],
                'portal_document_client_idx'
            );

            $table->index(
                ['project_id', 'published_at'],
                'portal_document_project_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'client_portal_documents'
        );
    }
};
