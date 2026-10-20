<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AssetsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RetireAssetRequest;
use App\Http\Requests\Admin\StoreAssetRequest;
use App\Models\Asset;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\User;
use App\Services\Assets\AssetLifecycleService;
use App\Services\Assets\AssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController extends Controller
{
    public function index(
        Request $request
    ): Response {
        $assets =
            Asset::query()
                ->latest('id')
                ->get()
                ->map(
                    fn (Asset $asset): array => [
                        'public_id' => $asset->public_id,

                        'asset_number' => $asset->asset_number,

                        'asset_tag' => $asset->asset_tag,

                        'name' => $asset->name,

                        'category' => $asset->category,

                        'brand' => $asset->brand,

                        'model' => $asset->model,

                        'serial_number' => $asset->serial_number,

                        'status' => $asset->status,

                        'location' => $asset->location,

                        'currency' => $asset->currency,

                        'purchase_cost' => $asset->purchase_cost,
                    ]
                );

        return Inertia::render(
            'admin/assets/index',
            [
                'assets' => $assets,

                'suppliers' => Supplier::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                    ]),

                'projects' => Project::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                    ]),

                'permissions' => [
                    'create' => $request
                        ->user()
                        ->can(
                            'assets.create'
                        ),

                    'export' => $request
                        ->user()
                        ->can(
                            'assets.export'
                        ),
                ],
            ]
        );
    }

    public function store(
        StoreAssetRequest $request,
        AssetService $service
    ): RedirectResponse {
        $asset =
            $service->create(
                $request->validated(),
                $request->user()
            );

        return to_route(
            'admin.assets.show',
            $asset
        );
    }

    public function show(
        Request $request,
        Asset $asset
    ): Response {
        $asset->load([
            'supplier:id,name',
            'project:id,name',
            'assignments.user:id,name,email',
            'assignments.project:id,name',
            'maintenances',
        ]);

        return Inertia::render(
            'admin/assets/show',
            [
                'asset' => [
                    'public_id' => $asset->public_id,

                    'asset_number' => $asset->asset_number,

                    'asset_tag' => $asset->asset_tag,

                    'name' => $asset->name,

                    'category' => $asset->category,

                    'brand' => $asset->brand,

                    'model' => $asset->model,

                    'serial_number' => $asset->serial_number,

                    'status' => $asset->status,

                    'location' => $asset->location,

                    'currency' => $asset->currency,

                    'purchase_cost' => $asset->purchase_cost,

                    'purchase_date' => $asset
                        ->purchase_date
                        ?->toDateString(),

                    'warranty_expires_at' => $asset
                        ->warranty_expires_at
                        ?->toDateString(),

                    'notes' => $asset->notes,

                    'supplier' => $asset->supplier
                            ? [
                                'name' => $asset
                                    ->supplier
                                    ->name,
                            ]
                            : null,

                    'project' => $asset->project
                            ? [
                                'name' => $asset
                                    ->project
                                    ->name,
                            ]
                            : null,

                    'assignments' => $asset
                        ->assignments
                        ->sortByDesc('id')
                        ->values()
                        ->map(
                            fn ($assignment): array => [
                                'public_id' => $assignment
                                    ->public_id,

                                'assigned_date' => $assignment
                                    ->assigned_date
                                    ->toDateString(),

                                'expected_return_date' => $assignment
                                    ->expected_return_date
                                    ?->toDateString(),

                                'returned_date' => $assignment
                                    ->returned_date
                                    ?->toDateString(),

                                'status' => $assignment
                                    ->status,

                                'assignment_notes' => $assignment
                                    ->assignment_notes,

                                'return_notes' => $assignment
                                    ->return_notes,

                                'user' => [
                                    'name' => $assignment
                                        ->user
                                        ->name,

                                    'email' => $assignment
                                        ->user
                                        ->email,
                                ],

                                'project' => $assignment
                                    ->project
                                        ? [
                                            'name' => $assignment
                                                ->project
                                                ->name,
                                        ]
                                        : null,
                            ]
                        ),

                    'maintenances' => $asset
                        ->maintenances
                        ->sortByDesc('id')
                        ->values()
                        ->map(
                            fn ($maintenance): array => [
                                'public_id' => $maintenance
                                    ->public_id,

                                'type' => $maintenance
                                    ->type,

                                'status' => $maintenance
                                    ->status,

                                'started_date' => $maintenance
                                    ->started_date
                                    ?->toDateString(),

                                'completed_date' => $maintenance
                                    ->completed_date
                                    ?->toDateString(),

                                'vendor' => $maintenance
                                    ->vendor,

                                'currency' => $maintenance
                                    ->currency,

                                'cost' => $maintenance
                                    ->cost,

                                'description' => $maintenance
                                    ->description,

                                'resolution' => $maintenance
                                    ->resolution,
                            ]
                        ),
                ],

                'users' => User::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                        'email',
                    ]),

                'projects' => Project::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                    ]),

                'permissions' => [
                    'assign' => $request
                        ->user()
                        ->can(
                            'assets.assign'
                        ),

                    'return' => $request
                        ->user()
                        ->can(
                            'assets.return'
                        ),

                    'maintain' => $request
                        ->user()
                        ->can(
                            'assets.maintain'
                        ),

                    'retire' => $request
                        ->user()
                        ->can(
                            'assets.retire'
                        ),
                ],
            ]
        );
    }

    public function retire(
        RetireAssetRequest $request,
        Asset $asset,
        AssetLifecycleService $service
    ): RedirectResponse {
        $service->retire(
            $asset,
            $request->validated()['reason'],
            $request->user()
        );

        return back();
    }

    public function excel(): BinaryFileResponse
    {
        return Excel::download(
            new AssetsExport,
            'assets.xlsx'
        );
    }
}
