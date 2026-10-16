<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreChangeRequestEstimateRequest;
use App\Models\ChangeRequest;
use Illuminate\Http\RedirectResponse;

class ChangeRequestEstimateController extends Controller
{
    public function store(
        StoreChangeRequestEstimateRequest $request,
        ChangeRequest $changeRequest
    ): RedirectResponse {
        abort_unless(
            in_array(
                $changeRequest->status,
                [
                    'submitted',
                    'under_review',
                    'awaiting_client',
                ],
                true
            ),
            409
        );

        $changeRequest
            ->estimate()
            ->updateOrCreate(
                [
                    'change_request_id' => $changeRequest->id,
                ],
                [
                    ...$request->validated(),

                    'currency' => strtoupper(
                        $request->validated(
                            'currency'
                        )
                    ),

                    'created_by' => $request->user()->id,
                ]
            );

        if (
            $changeRequest->status
            === 'submitted'
        ) {
            $changeRequest->update([
                'status' => 'under_review',
            ]);
        }

        return back()->with(
            'success',
            'Change request estimate saved.'
        );
    }
}
