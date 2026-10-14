<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('invoice_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')
                ->default(1);

            $table->string('description');

            $table->decimal('quantity', 12, 2)
                ->default(1);

            $table->decimal('unit_price', 15, 2);

            $table->decimal('tax_rate', 8, 4)
                ->default(0);

            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);

            $table->string('source_type', 50)
                ->nullable();

            $table->unsignedBigInteger('source_id')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['invoice_id', 'sort_order'],
                'invoice_line_sort_idx'
            );

            $table->unique(
                ['source_type', 'source_id'],
                'invoice_line_source_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'invoice_lines'
        );
    }
};
