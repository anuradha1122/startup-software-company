<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompleteAssetMaintenanceRequest;
use App\Http\Requests\Admin\StoreAssetMaintenanceRequest;
use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Services\Assets\AssetMaintenanceService;
use Illuminate\Http\RedirectResponse;

class AssetMaintenanceController extends Controller
{
    public function store(
        StoreAssetMaintenanceRequest $request,
        Asset $asset,
        AssetMaintenanceService $service
    ): RedirectResponse {
        $service->create(
            $asset,
            $request->validated(),
            $request->user()
        );

        return back();
    }

    public function complete(
        CompleteAssetMaintenanceRequest $request,
        AssetMaintenance $maintenance,
        AssetMaintenanceService $service
    ): RedirectResponse {
        $service->complete(
            $maintenance,
            $request->validated(),
            $request->user()
        );

        return back();
    }
}
