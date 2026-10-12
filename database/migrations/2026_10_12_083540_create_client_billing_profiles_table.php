<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_billing_profiles', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('client_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('billing_name');

            $table->string('billing_email')
                ->nullable();

            $table->string('billing_phone', 30)
                ->nullable();

            $table->text('billing_address')
                ->nullable();

            $table->string('city', 100)
                ->nullable();

            $table->string('country', 100)
                ->default('Sri Lanka');

            $table->string('tax_number', 100)
                ->nullable();

            $table->char('currency', 3)
                ->default('LKR');

            $table->unsignedSmallInteger('payment_terms_days')
                ->default(30);

            $table->text('invoice_notes')
                ->nullable();

            $table->boolean('is_default')
                ->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'client_id',
                'is_default',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'client_billing_profiles'
        );
    }
};
