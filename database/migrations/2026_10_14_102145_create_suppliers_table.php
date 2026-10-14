<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('supplier_number', 80)->unique();

            $table->string('name');
            $table->string('legal_name')->nullable();

            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('website')->nullable();

            $table->string('tax_number', 100)->nullable();

            $table->text('address')->nullable();
            $table->string('city', 150)->nullable();
            $table->string('country', 150)->nullable();

            $table->char('default_currency', 3)
                ->default('LKR');

            $table->enum('status', [
                'active',
                'inactive',
            ])->default('active');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['status', 'name'],
                'supplier_status_name_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
