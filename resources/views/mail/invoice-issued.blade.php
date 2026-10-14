<p>Hello {{ $invoice->billing_name }},</p>

<p>
    Please find attached invoice
    <strong>{{ $invoice->invoice_number }}</strong>.
</p>

<p>
    Amount:
    {{ $invoice->currency }}
    {{ number_format((float) $invoice->total_amount, 2) }}
</p>

<p>
    Due:
    {{ $invoice->due_date->format('Y-m-d') }}
</p>

<p>Thank you.</p>