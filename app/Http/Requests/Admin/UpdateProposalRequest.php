<?php

namespace App\Http\Requests\Admin;

class UpdateProposalRequest extends StoreProposalRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'proposals.update'
        ) ?? false;
    }
}
