<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AssetAssignmentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignAssetRequest;
use App\Http\Requests\Admin\ReturnAssetRequest;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use App\Services\Assets\AssetAssignmentService;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetAssignmentController extends Controller
{
    public function store(
        AssignAssetRequest $request,
        Asset $asset,
        AssetAssignmentService $service
    ): RedirectResponse {
        $validated =
            $request->validated();

        $recipient =
            User::query()
                ->findOrFail(
                    $validated['user_id']
                );

        $service->assign(
            $asset,
            $recipient,
            $validated,
            $request->user()
        );

        return back();
    }

    public function returnAsset(
        ReturnAssetRequest $request,
        AssetAssignment $assignment,
        AssetAssignmentService $service
    ): RedirectResponse {
        $service->returnAsset(
            $assignment,
            $request->validated(),
            $request->user()
        );

        return back();
    }

    public function excel(): BinaryFileResponse
    {
        return Excel::download(
            new AssetAssignmentsExport,
            'asset-assignments.xlsx'
        );
    }
}
