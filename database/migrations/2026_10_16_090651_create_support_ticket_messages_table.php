<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_ticket_messages', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('support_ticket_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('author_type', [
                'internal',
                'client',
            ]);

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('client_portal_user_id')
                ->nullable()
                ->constrained('client_portal_users')
                ->nullOnDelete();

            $table->text('message');

            /*
             * Internal-only notes never appear in portal DTOs.
             */
            $table->boolean('internal_only')
                ->default(false);

            $table->timestamps();

            $table->index(
                [
                    'support_ticket_id',
                    'created_at',
                ],
                'support_ticket_message_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'support_ticket_messages'
        );
    }
};
