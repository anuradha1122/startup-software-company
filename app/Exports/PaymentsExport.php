<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromArray;

class PaymentsExport implements FromArray
{
    public function array(): array
    {
        $rows = [[
            'Receipt',
            'Client',
            'Date',
            'Method',
            'Reference',
            'Currency',
            'Amount',
            'Allocated',
            'Unallocated',
        ]];

        $payments =
            Payment::query()
                ->with('client')
                ->latest(
                    'payment_date'
                )
                ->get();

        foreach ($payments as $payment) {
            $rows[] = [
                $payment->payment_number,
                $payment->client->name,
                $payment->payment_date
                    ->format('Y-m-d'),
                $payment->method,
                $payment->reference_number,
                $payment->currency,
                $payment->amount,
                $payment->allocated_amount,
                $payment->unallocated_amount,
            ];
        }

        return $rows;
    }
}
