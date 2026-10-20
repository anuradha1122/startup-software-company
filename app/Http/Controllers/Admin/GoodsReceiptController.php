<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGoodsReceiptRequest;
use App\Models\PurchaseOrder;
use App\Services\Procurement\GoodsReceiptService;
use Illuminate\Http\RedirectResponse;

class GoodsReceiptController extends Controller
{
    public function store(
        StoreGoodsReceiptRequest $request,
        PurchaseOrder $purchaseOrder,
        GoodsReceiptService $service
    ): RedirectResponse {
        $service->receive(
            $purchaseOrder,
            $request->validated(),
            $request->user()
        );

        return back();
    }
}
