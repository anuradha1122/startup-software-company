<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('contract_number', 30)->unique();

            $table->foreignId('quotation_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('client_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('requirement_version_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('estimate_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('status', [
                'draft',
                'issued',
                'signed',
                'cancelled',
                'expired',
            ])->default('draft');

            $table->string('title');

            $table->char('currency', 3);
            $table->decimal('contract_value', 15, 2);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->unsignedSmallInteger('payment_terms_days')
                ->default(30);

            $table->text('payment_schedule')->nullable();
            $table->text('special_terms')->nullable();
            $table->text('termination_terms')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('issued_at')->nullable();
            $table->timestamp('signed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique('quotation_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
