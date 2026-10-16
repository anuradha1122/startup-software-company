<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('code', 30)->unique();
            $table->string('name');

            $table->decimal('annual_entitlement', 8, 2)
                ->default(0);

            $table->boolean('paid')
                ->default(true);

            $table->boolean('requires_attachment')
                ->default(false);

            $table->boolean('allow_negative_balance')
                ->default(false);

            $table->boolean('active')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
