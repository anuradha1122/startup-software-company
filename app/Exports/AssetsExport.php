<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query(): Builder
    {
        return Asset::query()
            ->with([
                'supplier:id,name',
                'project:id,name',
            ])
            ->orderBy('asset_number');
    }

    public function headings(): array
    {
        return [
            'Asset Number',
            'Asset Tag',
            'Name',
            'Category',
            'Brand',
            'Model',
            'Serial Number',
            'Supplier',
            'Project',
            'Currency',
            'Purchase Cost',
            'Purchase Date',
            'Location',
            'Status',
        ];
    }

    public function map(
        mixed $asset
    ): array {
        return [
            $asset->asset_number,
            $asset->asset_tag,
            $asset->name,
            $asset->category,
            $asset->brand,
            $asset->model,
            $asset->serial_number,
            $asset->supplier?->name,
            $asset->project?->name,
            $asset->currency,
            $asset->purchase_cost,
            $asset->purchase_date?->format(
                'Y-m-d'
            ),
            $asset->location,
            $asset->status,
        ];
    }
}
