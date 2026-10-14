<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentAllocationRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Services\Billing\PaymentAllocationService;
use Illuminate\Http\RedirectResponse;

class PaymentAllocationController extends Controller
{
    public function store(
        StorePaymentAllocationRequest $request,
        Payment $payment,
        PaymentAllocationService $service
    ): RedirectResponse {
        $invoice =
            Invoice::findOrFail(
                $request->integer(
                    'invoice_id'
                )
            );

        $service->allocate(
            $payment,
            $invoice,
            (float) $request->validated(
                'amount'
            ),
            $request->user()
        );

        return back()->with(
            'success',
            'Payment allocated.'
        );
    }

    public function destroy(
        Payment $payment,
        PaymentAllocation $allocation,
        PaymentAllocationService $service
    ): RedirectResponse {
        abort_unless(
            request()->user()->can(
                'payments.allocate'
            ),
            403
        );

        abort_unless(
            $allocation->payment_id
            === $payment->id,
            404
        );

        $service->remove(
            $allocation
        );

        return back()->with(
            'success',
            'Payment allocation removed.'
        );
    }
}
