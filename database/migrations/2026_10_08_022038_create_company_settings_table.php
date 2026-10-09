<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table): void {
            $table->id();

            $table->string('legal_name');
            $table->string('trading_name')->nullable();

            $table->string('registration_number')->nullable();
            $table->string('tax_number')->nullable();

            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('website')->nullable();

            $table->text('address')->nullable();
            $table->string('country', 100)->default('Sri Lanka');
            $table->char('currency', 3)->default('LKR');
            $table->string('timezone', 100)->default('Asia/Colombo');

            $table->string('logo_path')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_name')->nullable();

            // Encrypted by the Eloquent model.
            $table->text('bank_account_number')->nullable();
            $table->text('bank_swift_code')->nullable();

            $table->text('document_footer')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
