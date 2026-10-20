<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProcurementRequestsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProcurementRequestRequest;
use App\Models\ProcurementRequest;
use App\Models\Project;
use App\Models\Supplier;
use App\Services\Procurement\ProcurementRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProcurementRequestController extends Controller
{
    public function index(
        Request $request
    ): Response {
        $requests =
            ProcurementRequest::query()
                ->with([
                    'project:id,public_id,name',
                    'requester:id,name',
                ])
                ->latest('id')
                ->get()
                ->map(
                    fn (ProcurementRequest $item): array => [
                        'public_id' => $item->public_id,

                        'request_number' => $item->request_number,

                        'title' => $item->title,

                        'priority' => $item->priority,

                        'status' => $item->status,

                        'currency' => $item->currency,

                        'estimated_total' => $item->estimated_total,

                        'project' => $item->project
                                ? [
                                    'public_id' => $item
                                        ->project
                                        ->public_id,

                                    'name' => $item
                                        ->project
                                        ->name,
                                ]
                                : null,

                        'requester' => [
                            'name' => $item
                                ->requester
                                ->name,
                        ],
                    ]
                );

        return Inertia::render(
            'admin/procurement/index',
            [
                'requests' => $requests,

                'projects' => Project::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'public_id',
                        'name',
                    ]),

                'permissions' => [
                    'create' => $request
                        ->user()
                        ->can(
                            'procurement-requests.create'
                        ),

                    'export' => $request
                        ->user()
                        ->can(
                            'procurement-requests.export'
                        ),
                ],
            ]
        );
    }

    public function store(
        StoreProcurementRequestRequest $request,
        ProcurementRequestService $service
    ): RedirectResponse {
        $item =
            $service->create(
                $request->validated(),
                $request->user()
            );

        return to_route(
            'admin.procurement.requests.show',
            $item
        );
    }

    public function show(
        Request $request,
        ProcurementRequest $procurementRequest
    ): Response {
        $procurementRequest->load([
            'items',
            'project:id,public_id,name',
            'requester:id,name',
            'reviewer:id,name',
            'purchaseOrders:id,public_id,purchase_order_number,procurement_request_id,status',
        ]);

        return Inertia::render(
            'admin/procurement/show',
            [
                'request' => [
                    'public_id' => $procurementRequest
                        ->public_id,

                    'request_number' => $procurementRequest
                        ->request_number,

                    'title' => $procurementRequest
                        ->title,

                    'business_reason' => $procurementRequest
                        ->business_reason,

                    'priority' => $procurementRequest
                        ->priority,

                    'status' => $procurementRequest
                        ->status,

                    'currency' => $procurementRequest
                        ->currency,

                    'estimated_total' => $procurementRequest
                        ->estimated_total,

                    'requester' => [
                        'name' => $procurementRequest
                            ->requester
                            ->name,
                    ],

                    'reviewer' => $procurementRequest
                        ->reviewer
                            ? [
                                'name' => $procurementRequest
                                    ->reviewer
                                    ->name,
                            ]
                            : null,

                    'project' => $procurementRequest
                        ->project
                            ? [
                                'public_id' => $procurementRequest
                                    ->project
                                    ->public_id,

                                'name' => $procurementRequest
                                    ->project
                                    ->name,
                            ]
                            : null,

                    'submitted_at' => $procurementRequest
                        ->submitted_at
                        ?->toIso8601String(),

                    'reviewed_at' => $procurementRequest
                        ->reviewed_at
                        ?->toIso8601String(),

                    'review_notes' => $procurementRequest
                        ->review_notes,

                    'items' => $procurementRequest
                        ->items
                        ->map(
                            fn ($item): array => [
                                'id' => $item->id,

                                'public_id' => $item->public_id,

                                'description' => $item->description,

                                'quantity' => $item->quantity,

                                'unit' => $item->unit,

                                'estimated_unit_cost' => $item
                                    ->estimated_unit_cost,

                                'estimated_total' => $item
                                    ->estimated_total,

                                'notes' => $item->notes,
                            ]
                        ),

                    'purchase_orders' => $procurementRequest
                        ->purchaseOrders
                        ->map(
                            fn ($order): array => [
                                'public_id' => $order
                                    ->public_id,

                                'purchase_order_number' => $order
                                    ->purchase_order_number,

                                'status' => $order
                                    ->status,
                            ]
                        ),
                ],

                'suppliers' => Supplier::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'public_id',
                        'name',
                    ]),

                'permissions' => [
                    'submit' => $request
                        ->user()
                        ->can(
                            'procurement-requests.submit'
                        ),

                    'review' => $request
                        ->user()
                        ->can(
                            'procurement-requests.review'
                        ),

                    'create_purchase_order' => $request
                        ->user()
                        ->can(
                            'purchase-orders.create'
                        ),
                ],
            ]
        );
    }

    public function submit(
        Request $request,
        ProcurementRequest $procurementRequest,
        ProcurementRequestService $service
    ): RedirectResponse {
        abort_unless(
            $request
                ->user()
                ->can(
                    'procurement-requests.submit'
                ),
            403
        );

        $service->submit(
            $procurementRequest,
            $request->user()
        );

        return back();
    }

    public function excel(): BinaryFileResponse
    {
        return Excel::download(
            new ProcurementRequestsExport,
            'procurement-requests.xlsx'
        );
    }
}
