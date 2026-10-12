<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requirements', function (Blueprint $table): void {
            $table->foreignId('approved_version_id')
                ->nullable()
                ->after('approved_at')
                ->constrained('requirement_versions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('requirements', function (Blueprint $table): void {
            $table->dropConstrainedForeignId(
                'approved_version_id'
            );
        });
    }
};
