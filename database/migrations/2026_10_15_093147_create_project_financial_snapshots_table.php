<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_financial_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->ulid('public_id')->unique();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('snapshot_date');

            $table->char('currency', 3);

            $table->decimal(
                'contracted_revenue',
                15,
                2
            )->default(0);

            $table->decimal(
                'invoiced_revenue',
                15,
                2
            )->default(0);

            $table->decimal(
                'collected_cash',
                15,
                2
            )->default(0);

            $table->decimal(
                'labor_cost',
                15,
                2
            )->default(0);

            $table->decimal(
                'non_labor_cost',
                15,
                2
            )->default(0);

            $table->decimal(
                'actual_cost',
                15,
                2
            )->default(0);

            $table->decimal(
                'gross_profit',
                15,
                2
            )->default(0);

            $table->decimal(
                'gross_margin_percent',
                10,
                4
            )->nullable();

            $table->decimal(
                'unbilled_revenue',
                15,
                2
            )->default(0);

            $table->decimal(
                'outstanding_receivable',
                15,
                2
            )->default(0);

            $table->decimal(
                'collection_percent',
                10,
                4
            )->nullable();

            $table->decimal(
                'cost_consumption_percent',
                10,
                4
            )->nullable();

            $table->decimal(
                'invoice_progress_percent',
                10,
                4
            )->nullable();

            $table->unsignedInteger(
                'currency_mismatch_count'
            )->default(0);

            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                [
                    'project_id',
                    'snapshot_date',
                ],
                'project_finance_snapshot_unique'
            );

            $table->index(
                [
                    'snapshot_date',
                    'currency',
                ],
                'finance_snapshot_date_currency_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'project_financial_snapshots'
        );
    }
};
