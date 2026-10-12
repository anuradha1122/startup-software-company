<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromArray;

class ClientsExport implements FromArray
{
    public function __construct(
        private readonly array $filters = []
    ) {}

    public function array(): array
    {
        $rows = [[
            'Client Number',
            'Client Name',
            'Legal Name',
            'Email',
            'Phone',
            'Owner',
            'Country',
            'Currency',
            'Payment Terms',
            'Status',
            'Contacts',
            'Onboarded',
        ]];

        $this->query()
            ->with('owner')
            ->withCount('contacts')
            ->get()
            ->each(
                function (
                    Client $client
                ) use (&$rows): void {
                    $rows[] = [
                        $client->client_number,
                        $client->name,
                        $client->legal_name,
                        $client->email,
                        $client->phone,
                        $client->owner?->name,
                        $client->country,
                        $client->default_currency,
                        $client->default_payment_terms_days
                            .' days',
                        $client->status,
                        $client->contacts_count,
                        $client->onboarded_at
                            ?->format('Y-m-d'),
                    ];
                }
            );

        return $rows;
    }

    private function query(): Builder
    {
        return Client::query()
            ->latest()
            ->when(
                $this->filters['status']
                    ?? null,
                fn (
                    Builder $query,
                    string $status
                ) => $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $this->filters['owner_id']
                    ?? null,
                fn (
                    Builder $query,
                    mixed $owner
                ) => $query->where(
                    'owner_id',
                    $owner
                )
            );
    }
}
