<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromArray;
use Spatie\Activitylog\Models\Activity;

class AuditLogsExport implements FromArray
{
    public function __construct(
        private readonly array $filters = [],
    ) {}

    public function array(): array
    {
        $rows = [
            [
                'Date',
                'Log',
                'Event',
                'Description',
                'User',
                'Subject Type',
                'Subject ID',
            ],
        ];

        $this->query()
            ->with('causer')
            ->get()
            ->each(
                function (
                    Activity $activity
                ) use (&$rows): void {
                    $rows[] = [
                        $activity->created_at
                            ?->format('Y-m-d H:i:s'),
                        $activity->log_name,
                        $activity->event,
                        $activity->description,
                        $activity->causer?->name,
                        $activity->subject_type,
                        $activity->subject_id,
                    ];
                }
            );

        return $rows;
    }

    private function query(): Builder
    {
        return Activity::query()
            ->latest()
            ->when(
                $this->filters['search'] ?? null,
                function (
                    Builder $query,
                    string $search
                ): void {
                    $query->where(
                        function (
                            Builder $inner
                        ) use ($search): void {
                            $inner
                                ->where(
                                    'description',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'event',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $this->filters['log_name'] ?? null,
                fn (
                    Builder $query,
                    string $logName
                ) => $query->where(
                    'log_name',
                    $logName
                )
            )
            ->when(
                $this->filters['date_from'] ?? null,
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'created_at',
                    '>=',
                    $date
                )
            )
            ->when(
                $this->filters['date_to'] ?? null,
                fn (
                    Builder $query,
                    string $date
                ) => $query->whereDate(
                    'created_at',
                    '<=',
                    $date
                )
            );
    }
}
