<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientContactRequest;
use App\Models\Client;
use App\Models\ClientContact;
use App\Services\Clients\ClientNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ClientContactController extends Controller
{
    public function store(
        StoreClientContactRequest $request,
        Client $client,
        ClientNotificationService $notifications
    ): RedirectResponse {
        $contact = DB::transaction(
            function () use (
                $request,
                $client
            ): ClientContact {
                $data =
                    $request->validated();

                if (
                    $data['is_primary']
                    ?? false
                ) {
                    $client
                        ->contacts()
                        ->update([
                            'is_primary' => false,
                        ]);
                }

                if (
                    $data[
                        'is_billing_contact'
                    ] ?? false
                ) {
                    $client
                        ->contacts()
                        ->update([
                            'is_billing_contact' => false,
                        ]);
                }

                return $client
                    ->contacts()
                    ->create($data);
            }
        );

        $notifications
            ->contactAdded(
                $client,
                $contact
            );

        return back()->with(
            'success',
            'Contact added.'
        );
    }

    public function update(
        StoreClientContactRequest $request,
        Client $client,
        ClientContact $contact
    ): RedirectResponse {
        abort_unless(
            $contact->client_id
                === $client->id,
            404
        );

        DB::transaction(
            function () use (
                $request,
                $client,
                $contact
            ): void {
                $data =
                    $request->validated();

                if (
                    $data['is_primary']
                    ?? false
                ) {
                    $client
                        ->contacts()
                        ->whereKeyNot(
                            $contact->id
                        )
                        ->update([
                            'is_primary' => false,
                        ]);
                }

                if (
                    $data[
                        'is_billing_contact'
                    ] ?? false
                ) {
                    $client
                        ->contacts()
                        ->whereKeyNot(
                            $contact->id
                        )
                        ->update([
                            'is_billing_contact' => false,
                        ]);
                }

                $contact->update($data);
            }
        );

        return back()->with(
            'success',
            'Contact updated.'
        );
    }

    public function destroy(
        Client $client,
        ClientContact $contact
    ): RedirectResponse {
        abort_unless(
            request()->user()->can(
                'clients.contacts.manage'
            ),
            403
        );

        abort_unless(
            $contact->client_id
                === $client->id,
            404
        );

        $contact->delete();

        return back()->with(
            'success',
            'Contact archived.'
        );
    }
}
