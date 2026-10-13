<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_profile_skill', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('resource_profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedTinyInteger(
                'proficiency'
            )->default(1);

            $table->unsignedSmallInteger(
                'years_experience'
            )->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'resource_profile_id',
                    'skill_id',
                ],
                'resource_skill_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'resource_profile_skill'
        );
    }
};
