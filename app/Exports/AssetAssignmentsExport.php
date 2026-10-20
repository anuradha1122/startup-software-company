<?php

namespace App\Exports;

use App\Models\AssetAssignment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetAssignmentsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return AssetAssignment::query()
            ->with([
                'asset:id,asset_number,asset_tag,name',
                'user:id,name,email',
                'project:id,name',
            ])
            ->orderByDesc('id');
    }

    public function headings(): array
    {
        return [
            'Asset Number',
            'Asset Tag',
            'Asset',
            'Assigned To',
            'Email',
            'Project',
            'Assigned Date',
            'Expected Return',
            'Returned Date',
            'Status',
        ];
    }

    public function map(
        mixed $assignment
    ): array {
        return [
            $assignment->asset?->asset_number,
            $assignment->asset?->asset_tag,
            $assignment->asset?->name,
            $assignment->user?->name,
            $assignment->user?->email,
            $assignment->project?->name,
            $assignment->assigned_date?->format(
                'Y-m-d'
            ),
            $assignment->expected_return_date?->format(
                'Y-m-d'
            ),
            $assignment->returned_date?->format(
                'Y-m-d'
            ),
            $assignment->status,
        ];
    }
}
