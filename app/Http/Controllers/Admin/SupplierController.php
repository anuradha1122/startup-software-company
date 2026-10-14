<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupplierRequest;
use App\Http\Requests\Admin\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(
        Request $request
    ): Response {
        Gate::authorize(
            'viewAny',
            Supplier::class
        );

        $suppliers =
            Supplier::query()
                ->when(
                    $request->filled('search'),
                    function ($query) use (
                        $request
                    ): void {
                        $search =
                            (string) $request->string(
                                'search'
                            );

                        $query->where(
                            function ($inner) use (
                                $search
                            ): void {
                                $inner
                                    ->where(
                                        'supplier_number',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->orderBy('name')
                ->paginate(25)
                ->withQueryString();

        return Inertia::render(
            'admin/suppliers/index',
            [
                'suppliers' => $suppliers,

                'filters' => $request->only(
                    'search'
                ),
            ]
        );
    }

    public function create(): Response
    {
        abort_unless(
            request()->user()->can(
                'suppliers.create'
            ),
            403
        );

        return Inertia::render(
            'admin/suppliers/create'
        );
    }

    public function store(
        StoreSupplierRequest $request,
        DocumentNumberService $numbers
    ): RedirectResponse {
        $supplier =
            Supplier::create([
                ...$request->validated(),

                'supplier_number' => $numbers->next(
                    'supplier'
                ),

                'default_currency' => strtoupper(
                    $request->validated(
                        'default_currency'
                    )
                ),

                'status' => 'active',

                'created_by' => $request->user()->id,
            ]);

        return redirect()
            ->route(
                'admin.suppliers.show',
                $supplier
            )
            ->with(
                'success',
                'Supplier created.'
            );
    }

    public function show(
        Supplier $supplier
    ): Response {
        Gate::authorize(
            'view',
            $supplier
        );

        $supplier->load([
            'expenses.project',
        ]);

        return Inertia::render(
            'admin/suppliers/show',
            [
                'supplier' => $supplier,
            ]
        );
    }

    public function update(
        UpdateSupplierRequest $request,
        Supplier $supplier
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $supplier
        );

        $data =
            $request->validated();

        $data['default_currency'] =
            strtoupper(
                $data[
                    'default_currency'
                ]
            );

        $supplier->update($data);

        return back()->with(
            'success',
            'Supplier updated.'
        );
    }

    public function destroy(
        Supplier $supplier
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $supplier
        );

        $supplier->delete();

        return redirect()
            ->route(
                'admin.suppliers.index'
            )
            ->with(
                'success',
                'Supplier deleted.'
            );
    }
}
