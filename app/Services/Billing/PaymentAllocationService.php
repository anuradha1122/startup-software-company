<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentAllocationService
{
    public function __construct(
        private readonly InvoiceStatusService $statuses
    ) {}

    public function allocate(
        Payment $payment,
        Invoice $invoice,
        float $amount,
        User $user
    ): PaymentAllocation {
        if ($amount <= 0) {
            throw new RuntimeException(
                'Allocation amount must be greater than zero.'
            );
        }

        return DB::transaction(
            function () use (
                $payment,
                $invoice,
                $amount,
                $user
            ): PaymentAllocation {
                $payment =
                    Payment::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $payment->id
                        );

                $invoice =
                    Invoice::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $invoice->id
                        );

                if (
                    $payment->client_id
                    !== $invoice->client_id
                ) {
                    throw new RuntimeException(
                        'Payment and invoice must belong to the same client.'
                    );
                }

                if (
                    strtoupper(
                        $payment->currency
                    )
                    !== strtoupper(
                        $invoice->currency
                    )
                ) {
                    throw new RuntimeException(
                        'Payment and invoice currencies must match.'
                    );
                }

                if (
                    ! $invoice->isOutstanding()
                ) {
                    throw new RuntimeException(
                        'The invoice is not outstanding.'
                    );
                }

                $remainingPayment =
                    round(
                        (float) $payment
                            ->unallocated_amount,
                        2
                    );

                $balance =
                    round(
                        (float) $invoice
                            ->balance_due,
                        2
                    );

                if ($amount > $remainingPayment) {
                    throw new RuntimeException(
                        'Allocation exceeds the unallocated payment amount.'
                    );
                }

                if ($amount > $balance) {
                    throw new RuntimeException(
                        'Allocation exceeds the invoice balance.'
                    );
                }

                if (
                    PaymentAllocation::query()
                        ->where(
                            'payment_id',
                            $payment->id
                        )
                        ->where(
                            'invoice_id',
                            $invoice->id
                        )
                        ->exists()
                ) {
                    throw new RuntimeException(
                        'This payment is already allocated to the invoice.'
                    );
                }

                $allocation =
                    PaymentAllocation::create([
                        'payment_id' => $payment->id,

                        'invoice_id' => $invoice->id,

                        'amount' => round(
                            $amount,
                            2
                        ),

                        'created_by' => $user->id,
                    ]);

                $payment->increment(
                    'allocated_amount',
                    $amount
                );

                $payment->decrement(
                    'unallocated_amount',
                    $amount
                );

                $invoice->increment(
                    'amount_paid',
                    $amount
                );

                $invoice->decrement(
                    'balance_due',
                    $amount
                );

                $invoice->refresh();

                $this->statuses
                    ->refresh(
                        $invoice
                    );

                return $allocation;
            }
        );
    }

    public function remove(
        PaymentAllocation $allocation
    ): void {
        DB::transaction(
            function () use (
                $allocation
            ): void {
                $payment =
                    Payment::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $allocation
                                ->payment_id
                        );

                $invoice =
                    Invoice::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $allocation
                                ->invoice_id
                        );

                $amount =
                    (float) $allocation
                        ->amount;

                $allocation->delete();

                $payment->decrement(
                    'allocated_amount',
                    $amount
                );

                $payment->increment(
                    'unallocated_amount',
                    $amount
                );

                $invoice->decrement(
                    'amount_paid',
                    $amount
                );

                $invoice->increment(
                    'balance_due',
                    $amount
                );

                $this->statuses
                    ->refresh(
                        $invoice->refresh()
                    );
            }
        );
    }
}
