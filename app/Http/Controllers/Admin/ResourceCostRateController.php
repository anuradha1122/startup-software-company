<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreResourceCostRateRequest;
use App\Models\ResourceCostRate;
use App\Models\User;
use App\Services\Timesheets\ResourceCostRateService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResourceCostRateController extends Controller
{
    public function index(
        Request $request,
        User $user
    ): Response {
        abort_unless(
            $request->user()->can(
                'internal-costs.view'
            ),
            403
        );

        return Inertia::render(
            'admin/people/cost-rates',
            [
                'person' => $user->only([
                    'id',
                    'public_id',
                    'name',
                    'email',
                ]),

                'rates' => $user
                    ->resourceCostRates()
                    ->latest(
                        'effective_from'
                    )
                    ->get(),

                'canManage' => $request->user()->can(
                    'internal-costs.manage-rates'
                ),
            ]
        );
    }

    public function store(
        StoreResourceCostRateRequest $request,
        User $user,
        ResourceCostRateService $service
    ): RedirectResponse {
        $from =
            Carbon::parse(
                $request->validated(
                    'effective_from'
                )
            );

        $to =
            $request->validated(
                'effective_to'
            )
                ? Carbon::parse(
                    $request->validated(
                        'effective_to'
                    )
                )
                : null;

        $service->assertNoOverlap(
            $user->id,
            $from,
            $to
        );

        ResourceCostRate::create([
            'user_id' => $user->id,

            'currency' => strtoupper(
                $request->validated(
                    'currency'
                )
            ),

            'hourly_cost_rate' => $request->validated(
                'hourly_cost_rate'
            ),

            'effective_from' => $from->toDateString(),

            'effective_to' => $to?->toDateString(),

            'active' => true,

            'notes' => $request->validated(
                'notes'
            ),

            'created_by' => $request->user()->id,
        ]);

        return back()->with(
            'success',
            'Internal cost rate created.'
        );
    }

    public function deactivate(
        Request $request,
        ResourceCostRate $rate
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'internal-costs.manage-rates'
            ),
            403
        );

        $rate->update([
            'active' => false,
        ]);

        return back()->with(
            'success',
            'Internal cost rate deactivated.'
        );
    }
}
