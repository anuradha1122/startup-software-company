<?php

namespace App\Http\Requests\Admin;

class UpdateQuotationRequest extends StoreQuotationRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'quotations.update'
        ) ?? false;
    }
}
