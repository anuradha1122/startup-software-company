<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_contacts', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            $table->index([
                'lead_id',
                'is_primary',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_contacts');
    }
};
