<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchaseOrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePurchaseOrderRequest;
use App\Models\ProcurementRequest;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\Procurement\PurchaseOrderService;
use App\Services\Procurement\PurchaseOrderWorkflowService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PurchaseOrderController extends Controller
{
    public function index(
        Request $request
    ): Response {
        $orders =
            PurchaseOrder::query()
                ->with([
                    'supplier:id,name',
                    'project:id,name',
                    'request:id,public_id,request_number',
                ])
                ->latest('id')
                ->get()
                ->map(
                    fn (PurchaseOrder $order): array => [
                        'public_id' => $order->public_id,

                        'purchase_order_number' => $order->purchase_order_number,

                        'currency' => $order->currency,

                        'total' => $order->total,

                        'order_date' => $order
                            ->order_date
                            ->toDateString(),

                        'expected_delivery_date' => $order
                            ->expected_delivery_date
                            ?->toDateString(),

                        'status' => $order->status,

                        'supplier' => [
                            'name' => $order
                                ->supplier
                                ->name,
                        ],

                        'project' => $order->project
                                ? [
                                    'name' => $order
                                        ->project
                                        ->name,
                                ]
                                : null,

                        'request' => $order->request
                                ? [
                                    'public_id' => $order
                                        ->request
                                        ->public_id,

                                    'request_number' => $order
                                        ->request
                                        ->request_number,
                                ]
                                : null,
                    ]
                );

        return Inertia::render(
            'admin/purchase-orders/index',
            [
                'orders' => $orders,

                'permissions' => [
                    'export' => $request
                        ->user()
                        ->can(
                            'purchase-orders.export'
                        ),
                ],
            ]
        );
    }

    public function store(
        StorePurchaseOrderRequest $request,
        PurchaseOrderService $service
    ): RedirectResponse {
        $validated =
            $request->validated();

        $procurementRequest =
            ProcurementRequest::query()
                ->where(
                    'public_id',
                    $validated[
                        'procurement_request_id'
                    ]
                )
                ->firstOrFail();

        $supplier =
            Supplier::query()
                ->findOrFail(
                    $validated[
                        'supplier_id'
                    ]
                );

        $order =
            $service
                ->createFromRequest(
                    $procurementRequest,
                    $supplier,
                    $validated,
                    $request->user()
                );

        return to_route(
            'admin.purchase-orders.show',
            $order
        );
    }

    public function show(
        Request $request,
        PurchaseOrder $purchaseOrder
    ): Response {
        $purchaseOrder->load([
            'supplier:id,name',
            'project:id,name',
            'request:id,public_id,request_number',
            'items',
            'receipts.receiver:id,name',
            'receipts.items',
        ]);

        return Inertia::render(
            'admin/purchase-orders/show',
            [
                'order' => [
                    'public_id' => $purchaseOrder
                        ->public_id,

                    'purchase_order_number' => $purchaseOrder
                        ->purchase_order_number,

                    'currency' => $purchaseOrder
                        ->currency,

                    'status' => $purchaseOrder
                        ->status,

                    'order_date' => $purchaseOrder
                        ->order_date
                        ->toDateString(),

                    'expected_delivery_date' => $purchaseOrder
                        ->expected_delivery_date
                        ?->toDateString(),

                    'subtotal' => $purchaseOrder
                        ->subtotal,

                    'tax_amount' => $purchaseOrder
                        ->tax_amount,

                    'total' => $purchaseOrder
                        ->total,

                    'supplier_reference' => $purchaseOrder
                        ->supplier_reference,

                    'notes' => $purchaseOrder
                        ->notes,

                    'supplier' => [
                        'name' => $purchaseOrder
                            ->supplier
                            ->name,
                    ],

                    'project' => $purchaseOrder
                        ->project
                            ? [
                                'name' => $purchaseOrder
                                    ->project
                                    ->name,
                            ]
                            : null,

                    'request' => $purchaseOrder
                        ->request
                            ? [
                                'public_id' => $purchaseOrder
                                    ->request
                                    ->public_id,

                                'request_number' => $purchaseOrder
                                    ->request
                                    ->request_number,
                            ]
                            : null,

                    'items' => $purchaseOrder
                        ->items
                        ->map(
                            fn ($item): array => [
                                'id' => $item->id,

                                'public_id' => $item->public_id,

                                'description' => $item->description,

                                'quantity' => $item->quantity,

                                'unit' => $item->unit,

                                'unit_cost' => $item->unit_cost,

                                'line_total' => $item->line_total,

                                'received_quantity' => $item
                                    ->received_quantity,

                                'asset_trackable' => $item
                                    ->asset_trackable,
                            ]
                        ),

                    'receipts' => $purchaseOrder
                        ->receipts
                        ->map(
                            fn ($receipt): array => [
                                'public_id' => $receipt
                                    ->public_id,

                                'receipt_number' => $receipt
                                    ->receipt_number,

                                'received_date' => $receipt
                                    ->received_date
                                    ->toDateString(),

                                'receiver' => [
                                    'name' => $receipt
                                        ->receiver
                                        ->name,
                                ],

                                'items' => $receipt
                                    ->items
                                    ->map(
                                        fn ($item): array => [
                                            'public_id' => $item
                                                ->public_id,

                                            'quantity_received' => $item
                                                ->quantity_received,

                                            'quantity_rejected' => $item
                                                ->quantity_rejected,
                                        ]
                                    ),
                            ]
                        ),
                ],

                'permissions' => [
                    'issue' => $request
                        ->user()
                        ->can(
                            'purchase-orders.issue'
                        ),

                    'cancel' => $request
                        ->user()
                        ->can(
                            'purchase-orders.cancel'
                        ),

                    'receive' => $request
                        ->user()
                        ->can(
                            'goods-receipts.create'
                        ),

                    'pdf' => $request
                        ->user()
                        ->can(
                            'purchase-orders.view'
                        ),
                ],
            ]
        );
    }

    public function issue(
        Request $request,
        PurchaseOrder $purchaseOrder,
        PurchaseOrderWorkflowService $service
    ): RedirectResponse {
        $service->issue(
            $purchaseOrder,
            $request->user()
        );

        return back();
    }

    public function cancel(
        Request $request,
        PurchaseOrder $purchaseOrder,
        PurchaseOrderWorkflowService $service
    ): RedirectResponse {
        $service->cancel(
            $purchaseOrder,
            $request->user()
        );

        return back();
    }

    public function excel(): BinaryFileResponse
    {
        return Excel::download(
            new PurchaseOrdersExport,
            'purchase-orders.xlsx'
        );
    }

    public function pdf(
        PurchaseOrder $purchaseOrder
    ): HttpResponse {
        $purchaseOrder->load([
            'supplier',
            'project',
            'request',
            'items',
        ]);

        return Pdf::loadView(
            'pdf.purchase-order',
            [
                'order' => $purchaseOrder,
            ]
        )->stream(
            $purchaseOrder
                ->purchase_order_number
            .'.pdf'
        );
    }
}
