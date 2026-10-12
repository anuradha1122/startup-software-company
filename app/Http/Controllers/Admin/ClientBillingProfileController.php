<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientBillingProfileRequest;
use App\Models\Client;
use App\Models\ClientBillingProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ClientBillingProfileController extends Controller
{
    public function store(
        StoreClientBillingProfileRequest $request,
        Client $client
    ): RedirectResponse {
        DB::transaction(
            function () use (
                $request,
                $client
            ): void {
                $data =
                    $request->validated();

                if (
                    $data['is_default']
                    ?? false
                ) {
                    $client
                        ->billingProfiles()
                        ->update([
                            'is_default' => false,
                        ]);
                }

                $client
                    ->billingProfiles()
                    ->create($data);
            }
        );

        return back()->with(
            'success',
            'Billing profile added.'
        );
    }

    public function update(
        StoreClientBillingProfileRequest $request,
        Client $client,
        ClientBillingProfile $billingProfile
    ): RedirectResponse {
        abort_unless(
            $billingProfile->client_id
                === $client->id,
            404
        );

        DB::transaction(
            function () use (
                $request,
                $client,
                $billingProfile
            ): void {
                $data =
                    $request->validated();

                if (
                    $data['is_default']
                    ?? false
                ) {
                    $client
                        ->billingProfiles()
                        ->whereKeyNot(
                            $billingProfile->id
                        )
                        ->update([
                            'is_default' => false,
                        ]);
                }

                $billingProfile
                    ->update($data);
            }
        );

        return back()->with(
            'success',
            'Billing profile updated.'
        );
    }

    public function destroy(
        Client $client,
        ClientBillingProfile $billingProfile
    ): RedirectResponse {
        abort_unless(
            request()->user()->can(
                'clients.billing.manage'
            ),
            403
        );

        abort_unless(
            $billingProfile->client_id
                === $client->id,
            404
        );

        $billingProfile->delete();

        return back()->with(
            'success',
            'Billing profile archived.'
        );
    }
}
