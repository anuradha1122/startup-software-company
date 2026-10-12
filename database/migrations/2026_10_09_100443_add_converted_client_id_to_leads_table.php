<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->foreignId('converted_client_id')
                ->nullable()
                ->after('converted_at')
                ->constrained('clients')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropConstrainedForeignId(
                'converted_client_id'
            );
        });
    }
};
