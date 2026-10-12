<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClientsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\User;
use App\Services\Clients\ClientNotificationService;
use App\Services\DocumentNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ClientController extends Controller
{
    public function index(
        Request $request
    ): Response {
        Gate::authorize(
            'viewAny',
            Client::class
        );

        $clients = Client::query()
            ->with('owner')
            ->withCount([
                'contacts',
                'documents',
            ])
            ->when(
                $request->filled('search'),
                function ($query) use (
                    $request
                ): void {
                    $search =
                        $request->string(
                            'search'
                        );

                    $query->where(
                        function (
                            $inner
                        ) use ($search): void {
                            $inner
                                ->where(
                                    'client_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'legal_name',
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
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->string(
                        'status'
                    )
                )
            )
            ->when(
                $request->filled('owner_id'),
                fn ($query) => $query->where(
                    'owner_id',
                    $request->integer(
                        'owner_id'
                    )
                )
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render(
            'admin/clients/index',
            [
                'clients' => $clients,

                'owners' => $this->owners(),

                'filters' => $request->only([
                    'search',
                    'status',
                    'owner_id',
                ]),
            ]
        );
    }

    public function create(): Response
    {
        Gate::authorize(
            'create',
            Client::class
        );

        return Inertia::render(
            'admin/clients/create',
            [
                'owners' => $this->owners(),
            ]
        );
    }

    public function store(
        StoreClientRequest $request,
        DocumentNumberService $numbers,
        ClientNotificationService $notifications
    ): RedirectResponse {
        $client = Client::create([
            ...$request->validated(),

            'client_number' => $numbers->next('client'),

            'onboarded_at' => now(),

            'created_by' => $request->user()->id,
        ]);

        $notifications
            ->clientCreated($client);

        return redirect()
            ->route(
                'admin.clients.show',
                $client
            )
            ->with(
                'success',
                'Client created.'
            );
    }

    public function show(
        Client $client
    ): Response {
        Gate::authorize(
            'view',
            $client
        );

        $client->load([
            'owner',
            'sourceLead',
            'contacts' => fn ($query) => $query->orderByDesc(
                'is_primary'
            )->orderBy('name'),

            'billingProfiles' => fn ($query) => $query->orderByDesc(
                'is_default'
            ),

            'documents' => fn ($query) => $query
                ->with('uploader')
                ->latest(),
        ]);

        return Inertia::render(
            'admin/clients/show',
            [
                'client' => $client,
            ]
        );
    }

    public function edit(
        Client $client
    ): Response {
        Gate::authorize(
            'update',
            $client
        );

        return Inertia::render(
            'admin/clients/edit',
            [
                'client' => $client,
                'owners' => $this->owners(),
            ]
        );
    }

    public function update(
        UpdateClientRequest $request,
        Client $client
    ): RedirectResponse {
        $client->update(
            $request->validated()
        );

        return redirect()
            ->route(
                'admin.clients.show',
                $client
            )
            ->with(
                'success',
                'Client updated.'
            );
    }

    public function destroy(
        Client $client
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $client
        );

        $client->delete();

        return redirect()
            ->route(
                'admin.clients.index'
            )
            ->with(
                'success',
                'Client archived.'
            );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'clients.export'
            ),
            403
        );

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->event('export')
            ->log(
                'Client list exported'
            );

        return Excel::download(
            new ClientsExport(
                $request->only([
                    'status',
                    'owner_id',
                ])
            ),
            'clients-'
            .now()->format(
                'Y-m-d-His'
            )
            .'.xlsx'
        );
    }

    public function profilePdf(
        Request $request,
        Client $client
    ): HttpResponse {
        Gate::authorize(
            'view',
            $client
        );

        $client->load([
            'owner',
            'contacts',
            'billingProfiles',
        ]);

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $client
            )
            ->event('export')
            ->log(
                'Client profile PDF generated'
            );

        return Pdf::loadView(
            'pdf.client-profile',
            [
                'client' => $client,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )
            ->setPaper(
                'a4',
                'portrait'
            )
            ->download(
                "{$client->client_number}-profile.pdf"
            );
    }

    private function owners()
    {
        return User::query()
            ->where(
                'status',
                'active'
            )
            ->whereHas(
                'roles',
                fn ($query) => $query->whereIn(
                    'name',
                    [
                        'Director',
                        'Admin',
                        'Sales',
                        'Project Manager',
                    ]
                )
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
            ]);
    }
}
