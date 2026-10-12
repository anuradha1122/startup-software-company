<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ClientBillingProfileController;
use App\Http\Controllers\Admin\ClientContactController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientDocumentController;
use App\Http\Controllers\Admin\CompanySettingController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ContractDocumentController;
use App\Http\Controllers\Admin\EstimateController;
use App\Http\Controllers\Admin\LeadActivityController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\ProposalController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\RequirementApprovalController;
use App\Http\Controllers\Admin\RequirementAttachmentController;
use App\Http\Controllers\Admin\RequirementController;
use App\Http\Controllers\Admin\RequirementVersionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'verified',
    'active',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {

        Route::get(
            'users/export',
            [UserController::class, 'export']
        )
            ->middleware('permission:users.export')
            ->name('users.export');

        Route::get(
            'users',
            [UserController::class, 'index']
        )
            ->middleware('permission:users.view')
            ->name('users.index');

        Route::post(
            'users',
            [UserController::class, 'store']
        )
            ->middleware('permission:users.create')
            ->name('users.store');

        Route::get(
            'users/{user}/edit',
            [UserController::class, 'edit']
        )
            ->middleware('permission:users.update')
            ->name('users.edit');

        Route::put(
            'users/{user}',
            [UserController::class, 'update']
        )
            ->middleware('permission:users.update')
            ->name('users.update');

        Route::delete(
            'users/{user}',
            [UserController::class, 'destroy']
        )
            ->middleware('permission:users.delete')
            ->name('users.destroy');

        Route::get(
            'roles',
            [RoleController::class, 'index']
        )
            ->middleware('permission:roles.manage')
            ->name('roles.index');

        Route::post(
            'roles',
            [RoleController::class, 'store']
        )
            ->middleware('permission:roles.manage')
            ->name('roles.store');

        Route::put(
            'roles/{role}',
            [RoleController::class, 'update']
        )
            ->middleware('permission:roles.manage')
            ->name('roles.update');

        Route::delete(
            'roles/{role}',
            [RoleController::class, 'destroy']
        )
            ->middleware('permission:roles.manage')
            ->name('roles.destroy');

        Route::get(
            'company',
            [CompanySettingController::class, 'edit']
        )
            ->middleware('permission:company-settings.view')
            ->name('company.edit');

        Route::put(
            'company',
            [CompanySettingController::class, 'update']
        )
            ->middleware('permission:company-settings.update')
            ->name('company.update');

        Route::get(
            'audit',
            [AuditLogController::class, 'index']
        )
            ->middleware('permission:audit.view')
            ->name('audit.index');

        Route::get(
            'audit/excel',
            [AuditLogController::class, 'excel']
        )
            ->middleware('permission:audit.export')
            ->name('audit.excel');

        Route::get(
            'audit/pdf',
            [AuditLogController::class, 'pdf']
        )
            ->middleware('permission:audit.export')
            ->name('audit.pdf');

        Route::prefix('leads')
            ->name('leads.')
            ->group(function (): void {
                Route::get(
                    'export/excel',
                    [LeadController::class, 'excel']
                )
                    ->middleware(
                        'permission:leads.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pipeline-pdf',
                    [
                        LeadController::class,
                        'pipelinePdf',
                    ]
                )
                    ->middleware(
                        'permission:leads.export'
                    )
                    ->name('pipeline-pdf');

                Route::get(
                    '/',
                    [LeadController::class, 'index']
                )
                    ->middleware(
                        'permission:leads.view'
                    )
                    ->name('index');

                Route::get(
                    'create',
                    [LeadController::class, 'create']
                )
                    ->middleware(
                        'permission:leads.create'
                    )
                    ->name('create');

                Route::post(
                    '/',
                    [LeadController::class, 'store']
                )
                    ->middleware(
                        'permission:leads.create'
                    )
                    ->name('store');

                Route::get(
                    '{lead}',
                    [LeadController::class, 'show']
                )
                    ->middleware(
                        'permission:leads.view'
                    )
                    ->name('show');

                Route::get(
                    '{lead}/edit',
                    [LeadController::class, 'edit']
                )
                    ->middleware(
                        'permission:leads.update'
                    )
                    ->name('edit');

                Route::put(
                    '{lead}',
                    [LeadController::class, 'update']
                )
                    ->middleware(
                        'permission:leads.update'
                    )
                    ->name('update');

                Route::patch(
                    '{lead}/stage',
                    [
                        LeadController::class,
                        'updateStage',
                    ]
                )
                    ->middleware(
                        'permission:leads.update'
                    )
                    ->name('stage');

                Route::delete(
                    '{lead}',
                    [LeadController::class, 'destroy']
                )
                    ->middleware(
                        'permission:leads.delete'
                    )
                    ->name('destroy');

                Route::post(
                    '{lead}/activities',
                    [
                        LeadActivityController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:leads.update'
                    )
                    ->name('activities.store');

                Route::post(
                    '{lead}/convert',
                    [
                        LeadController::class,
                        'convert',
                    ]
                )
                    ->middleware(
                        'permission:leads.convert'
                    )
                    ->name('convert');
            });

        Route::prefix('clients')
            ->name('clients.')
            ->group(function (): void {

                Route::get(
                    'export/excel',
                    [
                        ClientController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:clients.export'
                    )
                    ->name('excel');

                Route::get(
                    '/',
                    [
                        ClientController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:clients.view'
                    )
                    ->name('index');

                Route::get(
                    'create',
                    [
                        ClientController::class,
                        'create',
                    ]
                )
                    ->middleware(
                        'permission:clients.create'
                    )
                    ->name('create');

                Route::post(
                    '/',
                    [
                        ClientController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:clients.create'
                    )
                    ->name('store');

                Route::get(
                    '{client}',
                    [
                        ClientController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:clients.view'
                    )
                    ->name('show');

                Route::get(
                    '{client}/edit',
                    [
                        ClientController::class,
                        'edit',
                    ]
                )
                    ->middleware(
                        'permission:clients.update'
                    )
                    ->name('edit');

                Route::put(
                    '{client}',
                    [
                        ClientController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:clients.update'
                    )
                    ->name('update');

                Route::delete(
                    '{client}',
                    [
                        ClientController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:clients.delete'
                    )
                    ->name('destroy');

                Route::get(
                    '{client}/profile-pdf',
                    [
                        ClientController::class,
                        'profilePdf',
                    ]
                )
                    ->middleware(
                        'permission:clients.view'
                    )
                    ->name('profile-pdf');

                Route::post(
                    '{client}/contacts',
                    [
                        ClientContactController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:clients.contacts.manage'
                    )
                    ->name('contacts.store');

                Route::put(
                    '{client}/contacts/{contact}',
                    [
                        ClientContactController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:clients.contacts.manage'
                    )
                    ->name('contacts.update');

                Route::delete(
                    '{client}/contacts/{contact}',
                    [
                        ClientContactController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:clients.contacts.manage'
                    )
                    ->name('contacts.destroy');

                Route::post(
                    '{client}/billing-profiles',
                    [
                        ClientBillingProfileController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:clients.billing.manage'
                    )
                    ->name(
                        'billing-profiles.store'
                    );

                Route::put(
                    '{client}/billing-profiles/{billingProfile}',
                    [
                        ClientBillingProfileController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:clients.billing.manage'
                    )
                    ->name(
                        'billing-profiles.update'
                    );

                Route::delete(
                    '{client}/billing-profiles/{billingProfile}',
                    [
                        ClientBillingProfileController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:clients.billing.manage'
                    )
                    ->name(
                        'billing-profiles.destroy'
                    );

                Route::post(
                    '{client}/documents',
                    [
                        ClientDocumentController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:clients.documents.manage'
                    )
                    ->name('documents.store');

                Route::get(
                    '{client}/documents/{document}/download',
                    [
                        ClientDocumentController::class,
                        'download',
                    ]
                )
                    ->middleware(
                        'permission:clients.documents.download'
                    )
                    ->name(
                        'documents.download'
                    );

                Route::delete(
                    '{client}/documents/{document}',
                    [
                        ClientDocumentController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:clients.documents.manage'
                    )
                    ->name(
                        'documents.destroy'
                    );
            });

        Route::prefix('requirements')
            ->name('requirements.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        RequirementController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:requirements.view'
                    )
                    ->name('index');

                Route::get(
                    'create',
                    [
                        RequirementController::class,
                        'create',
                    ]
                )
                    ->middleware(
                        'permission:requirements.create'
                    )
                    ->name('create');

                Route::post(
                    '/',
                    [
                        RequirementController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:requirements.create'
                    )
                    ->name('store');

                Route::get(
                    '{requirement}',
                    [
                        RequirementController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:requirements.view'
                    )
                    ->name('show');

                Route::get(
                    '{requirement}/versions/{version}/edit',
                    [
                        RequirementVersionController::class,
                        'edit',
                    ]
                )
                    ->middleware(
                        'permission:requirements.update'
                    )
                    ->name(
                        'versions.edit'
                    );

                Route::put(
                    '{requirement}/versions/{version}',
                    [
                        RequirementVersionController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:requirements.update'
                    )
                    ->name(
                        'versions.update'
                    );

                Route::post(
                    '{requirement}/revisions',
                    [
                        RequirementVersionController::class,
                        'createRevision',
                    ]
                )
                    ->middleware(
                        'permission:requirements.update'
                    )
                    ->name(
                        'revisions.store'
                    );

                Route::post(
                    '{requirement}/versions/{version}/review',
                    [
                        RequirementVersionController::class,
                        'submitReview',
                    ]
                )
                    ->middleware(
                        'permission:requirements.review'
                    )
                    ->name(
                        'versions.review'
                    );

                Route::post(
                    '{requirement}/versions/{version}/approval-request',
                    [
                        RequirementApprovalController::class,
                        'requestApproval',
                    ]
                )
                    ->middleware(
                        'permission:requirements.request-approval'
                    )
                    ->name(
                        'versions.approval-request'
                    );

                Route::post(
                    '{requirement}/versions/{version}/approvals/{approval}/decision',
                    [
                        RequirementApprovalController::class,
                        'decision',
                    ]
                )
                    ->middleware(
                        'permission:requirements.record-approval'
                    )
                    ->name(
                        'approvals.decision'
                    );

                Route::get(
                    '{requirement}/versions/{version}/srs-pdf',
                    [
                        RequirementController::class,
                        'srsPdf',
                    ]
                )
                    ->middleware(
                        'permission:requirements.export'
                    )
                    ->name(
                        'versions.srs-pdf'
                    );

                Route::post(
                    '{requirement}/versions/{version}/attachments',
                    [
                        RequirementAttachmentController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:requirements.attachments.manage'
                    )
                    ->name(
                        'attachments.store'
                    );

                Route::get(
                    '{requirement}/versions/{version}/attachments/{attachment}/download',
                    [
                        RequirementAttachmentController::class,
                        'download',
                    ]
                )
                    ->middleware(
                        'permission:requirements.attachments.download'
                    )
                    ->name(
                        'attachments.download'
                    );

                Route::delete(
                    '{requirement}/versions/{version}/attachments/{attachment}',
                    [
                        RequirementAttachmentController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:requirements.attachments.manage'
                    )
                    ->name(
                        'attachments.destroy'
                    );

                Route::get(
                    '{requirement}/versions/{version}/estimates/create',
                    [
                        EstimateController::class,
                        'create',
                    ]
                )
                    ->middleware(
                        'permission:estimates.create'
                    )
                    ->name(
                        'estimates.create'
                    );

                Route::post(
                    '{requirement}/versions/{version}/estimates',
                    [
                        EstimateController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:estimates.create'
                    )
                    ->name(
                        'estimates.store'
                    );
            });

        Route::prefix('estimates')
            ->name('estimates.')
            ->group(function (): void {
                Route::get(
                    '{estimate}/edit',
                    [
                        EstimateController::class,
                        'edit',
                    ]
                )
                    ->middleware(
                        'permission:estimates.view'
                    )
                    ->name('edit');

                Route::put(
                    '{estimate}',
                    [
                        EstimateController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:estimates.update'
                    )
                    ->name('update');

                Route::post(
                    '{estimate}/approve',
                    [
                        EstimateController::class,
                        'approve',
                    ]
                )
                    ->middleware(
                        'permission:estimates.approve'
                    )
                    ->name('approve');

                Route::get(
                    '{estimate}/excel',
                    [
                        EstimateController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:estimates.export'
                    )
                    ->name('excel');

                Route::get(
                    '{estimate}/pdf',
                    [
                        EstimateController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:estimates.export'
                    )
                    ->name('pdf');
            });

        Route::prefix('proposals')
            ->name('proposals.')
            ->group(function (): void {
                Route::get('/', [
                    ProposalController::class,
                    'index',
                ])
                    ->middleware(
                        'permission:proposals.view'
                    )
                    ->name('index');

                Route::get('create', [
                    ProposalController::class,
                    'create',
                ])
                    ->middleware(
                        'permission:proposals.create'
                    )
                    ->name('create');

                Route::post('/', [
                    ProposalController::class,
                    'store',
                ])
                    ->middleware(
                        'permission:proposals.create'
                    )
                    ->name('store');

                Route::get('{proposal}', [
                    ProposalController::class,
                    'show',
                ])
                    ->middleware(
                        'permission:proposals.view'
                    )
                    ->name('show');

                Route::put('{proposal}', [
                    ProposalController::class,
                    'update',
                ])
                    ->middleware(
                        'permission:proposals.update'
                    )
                    ->name('update');

                Route::post('{proposal}/approve', [
                    ProposalController::class,
                    'approve',
                ])
                    ->middleware(
                        'permission:proposals.approve'
                    )
                    ->name('approve');

                Route::post('{proposal}/send', [
                    ProposalController::class,
                    'send',
                ])
                    ->middleware(
                        'permission:proposals.send'
                    )
                    ->name('send');

                Route::post('{proposal}/decision', [
                    ProposalController::class,
                    'decision',
                ])
                    ->middleware(
                        'permission:proposals.record-decision'
                    )
                    ->name('decision');

                Route::get('{proposal}/pdf', [
                    ProposalController::class,
                    'pdf',
                ])
                    ->middleware(
                        'permission:proposals.export'
                    )
                    ->name('pdf');
            });

        Route::prefix('quotations')
            ->name('quotations.')
            ->group(function (): void {
                Route::get('/', [
                    QuotationController::class,
                    'index',
                ])
                    ->middleware(
                        'permission:quotations.view'
                    )
                    ->name('index');

                Route::get('create', [
                    QuotationController::class,
                    'create',
                ])
                    ->middleware(
                        'permission:quotations.create'
                    )
                    ->name('create');

                Route::post('/', [
                    QuotationController::class,
                    'store',
                ])
                    ->middleware(
                        'permission:quotations.create'
                    )
                    ->name('store');

                Route::get('{quotation}', [
                    QuotationController::class,
                    'show',
                ])
                    ->middleware(
                        'permission:quotations.view'
                    )
                    ->name('show');

                Route::put('{quotation}', [
                    QuotationController::class,
                    'update',
                ])
                    ->middleware(
                        'permission:quotations.update'
                    )
                    ->name('update');

                Route::post('{quotation}/approve', [
                    QuotationController::class,
                    'approve',
                ])
                    ->middleware(
                        'permission:quotations.approve'
                    )
                    ->name('approve');

                Route::post('{quotation}/send', [
                    QuotationController::class,
                    'send',
                ])
                    ->middleware(
                        'permission:quotations.send'
                    )
                    ->name('send');

                Route::post('{quotation}/decision', [
                    QuotationController::class,
                    'decision',
                ])
                    ->middleware(
                        'permission:quotations.record-decision'
                    )
                    ->name('decision');

                Route::get('{quotation}/excel', [
                    QuotationController::class,
                    'excel',
                ])
                    ->middleware(
                        'permission:quotations.export'
                    )
                    ->name('excel');

                Route::get('{quotation}/pdf', [
                    QuotationController::class,
                    'pdf',
                ])
                    ->middleware(
                        'permission:quotations.export'
                    )
                    ->name('pdf');

                Route::get('{quotation}/contract/create', [
                    ContractController::class,
                    'create',
                ])
                    ->middleware(
                        'permission:contracts.create'
                    )
                    ->name('contract.create');
            });

        Route::prefix('contracts')
            ->name('contracts.')
            ->group(function (): void {
                Route::get('/', [
                    ContractController::class,
                    'index',
                ])
                    ->middleware(
                        'permission:contracts.view'
                    )
                    ->name('index');

                Route::post('/', [
                    ContractController::class,
                    'store',
                ])
                    ->middleware(
                        'permission:contracts.create'
                    )
                    ->name('store');

                Route::get('{contract}', [
                    ContractController::class,
                    'show',
                ])
                    ->middleware(
                        'permission:contracts.view'
                    )
                    ->name('show');

                Route::post('{contract}/issue', [
                    ContractController::class,
                    'issue',
                ])
                    ->middleware(
                        'permission:contracts.issue'
                    )
                    ->name('issue');

                Route::post('{contract}/signed', [
                    ContractController::class,
                    'markSigned',
                ])
                    ->middleware(
                        'permission:contracts.mark-signed'
                    )
                    ->name('signed');

                Route::get('{contract}/pdf', [
                    ContractController::class,
                    'pdf',
                ])
                    ->middleware(
                        'permission:contracts.export'
                    )
                    ->name('pdf');

                Route::post('{contract}/documents', [
                    ContractDocumentController::class,
                    'store',
                ])
                    ->middleware(
                        'permission:contracts.documents.manage'
                    )
                    ->name('documents.store');

                Route::get(
                    '{contract}/documents/{document}/download',
                    [
                        ContractDocumentController::class,
                        'download',
                    ]
                )
                    ->middleware(
                        'permission:contracts.documents.download'
                    )
                    ->name('documents.download');
            });
    });
