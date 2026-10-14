<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceLineRequest;
use App\Http\Requests\Admin\UpdateInvoiceLineRequest;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Services\Billing\InvoiceCalculatorService;
use App\Services\Billing\InvoiceTotalsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class InvoiceLineController extends Controller
{
    public function store(
        StoreInvoiceLineRequest $request,
        Invoice $invoice,
        InvoiceCalculatorService $calculator,
        InvoiceTotalsService $totals
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $invoice
        );

        $data =
            $request->validated();

        $calculated =
            $calculator->line(
                (float) $data['quantity'],
                (float) $data['unit_price'],
                (float) $data['tax_rate']
            );

        $sortOrder =
            (int) $invoice
                ->lines()
                ->max('sort_order')
            + 1;

        $invoice
            ->lines()
            ->create([
                'sort_order' => $sortOrder,

                'description' => $data['description'],

                ...$calculated,
            ]);

        $totals->recalculate(
            $invoice
        );

        return back()->with(
            'success',
            'Invoice line added.'
        );
    }

    public function update(
        UpdateInvoiceLineRequest $request,
        Invoice $invoice,
        InvoiceLine $line,
        InvoiceCalculatorService $calculator,
        InvoiceTotalsService $totals
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $invoice
        );

        $this->assertBelongs(
            $invoice,
            $line
        );

        $data =
            $request->validated();

        $calculated =
            $calculator->line(
                (float) $data['quantity'],
                (float) $data['unit_price'],
                (float) $data['tax_rate']
            );

        $line->update([
            'description' => $data['description'],

            ...$calculated,
        ]);

        $totals->recalculate(
            $invoice
        );

        return back()->with(
            'success',
            'Invoice line updated.'
        );
    }

    public function destroy(
        Invoice $invoice,
        InvoiceLine $line,
        InvoiceTotalsService $totals
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $invoice
        );

        $this->assertBelongs(
            $invoice,
            $line
        );

        $line->delete();

        $totals->recalculate(
            $invoice
        );

        return back()->with(
            'success',
            'Invoice line removed.'
        );
    }

    private function assertBelongs(
        Invoice $invoice,
        InvoiceLine $line
    ): void {
        abort_unless(
            $line->invoice_id
            === $invoice->id,
            404
        );
    }
}
