<p>Hello {{ $payment->client->name }},</p>

<p>
    Thank you for your payment.
</p>

<p>
    Receipt:
    <strong>{{ $payment->payment_number }}</strong>
</p>

<p>
    Amount:
    {{ $payment->currency }}
    {{ number_format((float) $payment->amount, 2) }}
</p>

<p>
    Payment date:
    {{ $payment->payment_date->format('Y-m-d') }}
</p>