<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_contacts', function (Blueprint $table): void {
            $table->ulid('public_id')
                ->nullable()
                ->unique()
                ->after('id');

            $table->boolean('is_billing_contact')
                ->default(false)
                ->after('is_primary');

            $table->boolean('is_decision_maker')
                ->default(false);

            $table->string('status', 20)
                ->default('active');

            $table->text('notes')
                ->nullable();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('client_contacts', function (Blueprint $table): void {
            $table->dropUnique([
                'public_id',
            ]);

            $table->dropColumn([
                'public_id',
                'is_billing_contact',
                'is_decision_maker',
                'status',
                'notes',
                'deleted_at',
            ]);
        });
    }
};
