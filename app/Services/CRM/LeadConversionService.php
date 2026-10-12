<?php

namespace App\Services\CRM;

use App\Models\Client;
use App\Models\Lead;
use App\Models\LeadStage;
use App\Services\Clients\ClientNotificationService;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LeadConversionService
{
    public function __construct(
        private readonly DocumentNumberService $numbers,
        private readonly ClientNotificationService $notifications,
    ) {}

    public function convert(
        Lead $lead,
        int $userId
    ): Client {
        if ($lead->status === 'converted') {
            if ($lead->convertedClient) {
                return $lead->convertedClient;
            }

            throw new RuntimeException(
                'This lead was already converted.'
            );
        }

        $client = DB::transaction(function () use (
            $lead,
            $userId
        ): Client {
            $lead->load('contacts');

            $primaryContact = $lead->contacts
                ->firstWhere('is_primary', true)
                ?? $lead->contacts->first();

            $client = Client::create([
                'client_number' => $this->numbers->next('client'),

                'name' => $lead->company_name
                    ?: $lead->title,

                'email' => $primaryContact?->email,
                'phone' => $primaryContact?->phone,

                'default_currency' => $lead->currency ?? 'LKR',

                'status' => 'active',
                'onboarded_at' => now(),

                'source_lead_id' => $lead->id,
                'owner_id' => $lead->owner_id,
                'created_by' => $userId,
            ]);

            foreach ($lead->contacts as $contact) {
                $client->contacts()->create([
                    'name' => $contact->name,
                    'designation' => $contact->designation,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'is_primary' => $contact->is_primary,
                ]);
            }

            $wonStage = LeadStage::query()
                ->where('is_won', true)
                ->first();

            $lead->update([
                'status' => 'converted',
                'converted_at' => now(),
                'converted_client_id' => $client->id,
                'stage_id' => $wonStage?->id
                    ?? $lead->stage_id,
            ]);

            activity('crm-leads')
                ->causedBy(auth()->user())
                ->performedOn($lead)
                ->event('converted')
                ->withProperties([
                    'client_id' => $client->id,
                    'client_public_id' => $client->public_id,
                ])
                ->log('Lead converted to client');

            return $client;
        });

        $this->notifications->clientCreated($client);

        return $client;
    }
}
