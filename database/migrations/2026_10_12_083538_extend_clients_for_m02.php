<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->string('client_number', 30)
                ->nullable()
                ->unique()
                ->after('public_id');

            $table->string('legal_name')
                ->nullable()
                ->after('name');

            $table->string('registration_number', 100)
                ->nullable();

            $table->string('tax_number', 100)
                ->nullable();

            $table->string('website')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->string('city', 100)
                ->nullable();

            $table->string('country', 100)
                ->default('Sri Lanka');

            $table->char('default_currency', 3)
                ->default('LKR');

            $table->unsignedSmallInteger('default_payment_terms_days')
                ->default(30);

            $table->text('notes')
                ->nullable();

            $table->timestamp('onboarded_at')
                ->nullable();

            $table->index('status');
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->dropIndex(['status']);
            $table->dropIndex(['owner_id']);

            $table->dropUnique([
                'client_number',
            ]);

            $table->dropColumn([
                'client_number',
                'legal_name',
                'registration_number',
                'tax_number',
                'website',
                'address',
                'city',
                'country',
                'default_currency',
                'default_payment_terms_days',
                'notes',
                'onboarded_at',
            ]);
        });
    }
};
