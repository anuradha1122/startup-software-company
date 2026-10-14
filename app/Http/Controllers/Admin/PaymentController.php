<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PaymentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Mail\PaymentReceiptMail;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentRecordedNotification;
use App\Services\DocumentNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PaymentController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'payments.view'
            ),
            403
        );

        return Inertia::render(
            'admin/payments/index',
            [
                'payments' => Payment::query()
                    ->with('client')
                    ->latest(
                        'payment_date'
                    )
                    ->paginate(25),

                'clients' => Client::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'public_id',
                        'name',
                        'default_currency',
                    ]),
            ]
        );
    }

    public function store(
        StorePaymentRequest $request,
        DocumentNumberService $numbers
    ): RedirectResponse {
        $data =
            $request->validated();

        $payment =
            Payment::create([
                ...$data,

                'payment_number' => $numbers->next(
                    'receipt'
                ),

                'currency' => strtoupper(
                    $data['currency']
                ),

                'allocated_amount' => 0,

                'unallocated_amount' => $data['amount'],

                'recorded_by' => $request->user()->id,
            ]);

        $users =
            User::query()
                ->permission(
                    'payments.view'
                )
                ->where(
                    'status',
                    'active'
                )
                ->where(
                    'id',
                    '!=',
                    $request->user()->id
                )
                ->get();

        Notification::send(
            $users,
            new PaymentRecordedNotification(
                $payment
            )
        );

        return redirect()
            ->route(
                'admin.payments.show',
                $payment
            )
            ->with(
                'success',
                'Payment recorded.'
            );
    }

    public function show(
        Request $request,
        Payment $payment
    ): Response {
        abort_unless(
            $request->user()->can(
                'payments.view'
            ),
            403
        );

        $payment->load([
            'client.contacts',
            'allocations.invoice',
        ]);

        $invoices =
            $payment
                ->client
                ->invoices()
                ->whereIn(
                    'status',
                    [
                        'issued',
                        'partially_paid',
                        'overdue',
                    ]
                )
                ->where(
                    'currency',
                    $payment->currency
                )
                ->where(
                    'balance_due',
                    '>',
                    0
                )
                ->orderBy(
                    'due_date'
                )
                ->get();

        return Inertia::render(
            'admin/payments/show',
            [
                'payment' => $payment,

                'invoices' => $invoices,
            ]
        );
    }

    public function sendReceipt(
        Request $request,
        Payment $payment
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'payments.receipt.send'
            ),
            403
        );

        $payment->load(
            'client.contacts'
        );

        $email =
            $payment
                ->client
                ->contacts
                ->first(
                    fn ($contact) => ! empty(
                        $contact->email
                    )
                )
                ?->email;

        abort_unless(
            $email,
            422,
            'No client contact email is available.'
        );

        Mail::to($email)->queue(
            new PaymentReceiptMail(
                $payment
            )
        );

        $payment->update([
            'receipt_sent_at' => now(),
        ]);

        return back()->with(
            'success',
            'Receipt email queued.'
        );
    }

    public function pdf(
        Request $request,
        Payment $payment
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'payments.view'
            ),
            403
        );

        $payment->load([
            'client',
            'allocations.invoice',
        ]);

        return Pdf::loadView(
            'pdf.payment-receipt',
            [
                'payment' => $payment,

                'company' => CompanySetting::first(),
            ]
        )->download(
            "{$payment->payment_number}.pdf"
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'payments.export'
            ),
            403
        );

        return Excel::download(
            new PaymentsExport,
            'payments.xlsx'
        );
    }
}
