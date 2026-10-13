<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string('name', 150)->unique();

            $table->string('category', 150)
                ->nullable();

            $table->boolean('active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'active',
                'category',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
