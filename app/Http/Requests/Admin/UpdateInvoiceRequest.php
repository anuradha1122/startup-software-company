<?php

namespace App\Http\Requests\Admin;

class UpdateInvoiceRequest extends StoreInvoiceRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'invoices.update'
        ) ?? false;
    }
}
