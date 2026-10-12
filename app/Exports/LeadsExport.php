<?php

namespace App\Exports;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromArray;

class LeadsExport implements FromArray
{
    public function __construct(
        private readonly array $filters = []
    ) {}

    public function array(): array
    {
        $rows = [[
            'Lead Number',
            'Title',
            'Company',
            'Stage',
            'Source',
            'Owner',
            'Priority',
            'Estimated Value',
            'Currency',
            'Probability',
            'Next Follow Up',
            'Last Activity',
            'Status',
            'Created At',
        ]];

        $this->query()
            ->with([
                'stage',
                'source',
                'owner',
            ])
            ->get()
            ->each(
                function (Lead $lead) use (&$rows): void {
                    $rows[] = [
                        $lead->lead_number,
                        $lead->title,
                        $lead->company_name,
                        $lead->stage?->name,
                        $lead->source?->name,
                        $lead->owner?->name,
                        $lead->priority,
                        $lead->estimated_value,
                        $lead->currency,
                        $lead->probability.'%',
                        $lead->next_follow_up_at
                            ?->format('Y-m-d H:i'),
                        $lead->last_activity_at
                            ?->format('Y-m-d H:i'),
                        $lead->status,
                        $lead->created_at
                            ?->format('Y-m-d H:i'),
                    ];
                }
            );

        return $rows;
    }

    private function query(): Builder
    {
        return Lead::query()
            ->latest()
            ->when(
                $this->filters['source_id']
                    ?? null,
                fn (
                    Builder $query,
                    mixed $source
                ) => $query->where(
                    'source_id',
                    $source
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
            )
            ->when(
                $this->filters['stage_id']
                    ?? null,
                fn (
                    Builder $query,
                    mixed $stage
                ) => $query->where(
                    'stage_id',
                    $stage
                )
            );
    }
}
