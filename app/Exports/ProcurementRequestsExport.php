<?php

namespace App\Exports;

use App\Models\ProcurementRequest;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProcurementRequestsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return ProcurementRequest::query()
            ->with([
                'requester:id,name',
                'project:id,name',
            ])
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'Request Number',
            'Title',
            'Project',
            'Requester',
            'Priority',
            'Status',
            'Currency',
            'Estimated Total',
            'Submitted At',
            'Approved At',
        ];
    }

    public function map(
        mixed $request
    ): array {
        return [
            $request->request_number,
            $request->title,
            $request->project?->name,
            $request->requester?->name,
            $request->priority,
            $request->status,
            $request->currency,
            $request->estimated_total,
            $request->submitted_at?->format(
                'Y-m-d H:i:s'
            ),
            $request->approved_at?->format(
                'Y-m-d H:i:s'
            ),
        ];
    }
}
