<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_portal_users', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('client_contact_id')
                ->nullable()
                ->constrained('client_contacts')
                ->nullOnDelete();

            $table->string('name');
            $table->string('email')->unique();

            $table->timestamp('email_verified_at')
                ->nullable();

            $table->string('password')
                ->nullable();

            $table->enum('status', [
                'invited',
                'active',
                'suspended',
                'revoked',
            ])->default('invited');

            $table->timestamp('invited_at')
                ->nullable();

            $table->timestamp('activated_at')
                ->nullable();

            $table->timestamp('last_login_at')
                ->nullable();

            $table->foreignId('invited_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['client_id', 'status'],
                'portal_user_client_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'client_portal_users'
        );
    }
};
