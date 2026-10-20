<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->string(
                'asset_number',
                80
            )->unique();

            $table->string(
                'asset_tag',
                100
            )->unique();

            $table->string('name');

            $table->string(
                'category',
                100
            );

            $table->string('brand')
                ->nullable();

            $table->string('model')
                ->nullable();

            $table->string(
                'serial_number'
            )
                ->nullable()
                ->unique();

            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId(
                'purchase_order_item_id'
            )
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->char(
                'currency',
                3
            )->nullable();

            $table->decimal(
                'purchase_cost',
                15,
                2
            )->nullable();

            $table->date(
                'purchase_date'
            )->nullable();

            $table->date(
                'warranty_expires_at'
            )->nullable();

            $table->enum('status', [
                'available',
                'assigned',
                'maintenance',
                'lost',
                'retired',
                'disposed',
            ])->default('available');

            $table->string(
                'location'
            )->nullable();

            $table->text('notes')
                ->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamp(
                'retired_at'
            )->nullable();

            $table->foreignId(
                'retired_by'
            )
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text(
                'retirement_reason'
            )->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'status',
                'category',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
