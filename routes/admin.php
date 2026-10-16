<?php

use App\Http\Controllers\Admin\AdminOperationsController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ClientApprovalController;
use App\Http\Controllers\Admin\ClientBillingProfileController;
use App\Http\Controllers\Admin\ClientContactController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientDocumentController;
use App\Http\Controllers\Admin\ClientPortalDocumentController;
use App\Http\Controllers\Admin\ClientPortalUserController;
use App\Http\Controllers\Admin\CompanySettingController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ContractDocumentController;
use App\Http\Controllers\Admin\EstimateController;
use App\Http\Controllers\Admin\FinanceDashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\InvoiceLineController;
use App\Http\Controllers\Admin\LeadActivityController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\PaymentAllocationController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PeopleController;
use App\Http\Controllers\Admin\ProjectAssignmentController;
use App\Http\Controllers\Admin\ProjectCharterController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectCostController;
use App\Http\Controllers\Admin\ProjectDecisionController;
use App\Http\Controllers\Admin\ProjectExpenseApprovalController;
use App\Http\Controllers\Admin\ProjectExpenseAttachmentController;
use App\Http\Controllers\Admin\ProjectExpenseController;
use App\Http\Controllers\Admin\ProjectFinanceSettingController;
use App\Http\Controllers\Admin\ProjectFinancialSnapshotController;
use App\Http\Controllers\Admin\ProjectInternalCostController;
use App\Http\Controllers\Admin\ProjectMilestoneController;
use App\Http\Controllers\Admin\ProjectOperationsReportController;
use App\Http\Controllers\Admin\ProjectPhaseController;
use App\Http\Controllers\Admin\ProjectProfitabilityController;
use App\Http\Controllers\Admin\ProjectRiskController;
use App\Http\Controllers\Admin\ProjectStakeholderController;
use App\Http\Controllers\Admin\ProjectTaskCommentController;
use App\Http\Controllers\Admin\ProjectTaskController;
use App\Http\Controllers\Admin\ProjectTaskWorkflowController;
use App\Http\Controllers\Admin\ProposalController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\ReceivableController;
use App\Http\Controllers\Admin\ReportCenterController;
use App\Http\Controllers\Admin\ReportRunController;
use App\Http\Controllers\Admin\RequirementApprovalController;
use App\Http\Controllers\Admin\RequirementAttachmentController;
use App\Http\Controllers\Admin\RequirementController;
use App\Http\Controllers\Admin\RequirementVersionController;
use App\Http\Controllers\Admin\ResourceCostRateController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TimesheetApprovalController;
use App\Http\Controllers\Admin\TimesheetController;
use App\Http\Controllers\Admin\TimesheetEntryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WorkSubmissionAttachmentController;
use App\Http\Controllers\Admin\WorkSubmissionController;
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

        Route::prefix('projects')
            ->name('projects.')
            ->group(function (): void {
                Route::get(
                    'export/excel',
                    [
                        ProjectController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:projects.export'
                    )
                    ->name('excel');

                Route::get(
                    '/',
                    [
                        ProjectController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:projects.view'
                    )
                    ->name('index');

                Route::get(
                    'create',
                    [
                        ProjectController::class,
                        'create',
                    ]
                )
                    ->middleware(
                        'permission:projects.create'
                    )
                    ->name('create');

                Route::post(
                    '/',
                    [
                        ProjectController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:projects.create'
                    )
                    ->name('store');

                Route::get(
                    '{project}',
                    [
                        ProjectController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:projects.view'
                    )
                    ->name('show');

                Route::put(
                    '{project}',
                    [
                        ProjectController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:projects.update'
                    )
                    ->name('update');

                Route::post(
                    '{project}/activate',
                    [
                        ProjectController::class,
                        'activate',
                    ]
                )
                    ->middleware(
                        'permission:projects.activate'
                    )
                    ->name('activate');

                Route::post(
                    '{project}/hold',
                    [
                        ProjectController::class,
                        'hold',
                    ]
                )
                    ->middleware(
                        'permission:projects.update'
                    )
                    ->name('hold');

                Route::post(
                    '{project}/resume',
                    [
                        ProjectController::class,
                        'resume',
                    ]
                )
                    ->middleware(
                        'permission:projects.update'
                    )
                    ->name('resume');

                Route::post(
                    '{project}/complete',
                    [
                        ProjectController::class,
                        'complete',
                    ]
                )
                    ->middleware(
                        'permission:projects.complete'
                    )
                    ->name('complete');

                Route::post(
                    '{project}/kickoff',
                    [
                        ProjectController::class,
                        'kickoff',
                    ]
                )
                    ->middleware(
                        'permission:projects.kickoff.send'
                    )
                    ->name('kickoff');

                Route::get(
                    '{project}/charter-pdf',
                    [
                        ProjectController::class,
                        'charterPdf',
                    ]
                )
                    ->middleware(
                        'permission:projects.export'
                    )
                    ->name('charter-pdf');

                Route::get(
                    '{project}/charters/{charter}/edit',
                    [
                        ProjectCharterController::class,
                        'edit',
                    ]
                )
                    ->middleware(
                        'permission:projects.charter.manage'
                    )
                    ->name(
                        'charters.edit'
                    );

                Route::put(
                    '{project}/charters/{charter}',
                    [
                        ProjectCharterController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:projects.charter.manage'
                    )
                    ->name(
                        'charters.update'
                    );

                Route::post(
                    '{project}/charters/{charter}/approve',
                    [
                        ProjectCharterController::class,
                        'approve',
                    ]
                )
                    ->middleware(
                        'permission:projects.charter.approve'
                    )
                    ->name(
                        'charters.approve'
                    );

                Route::post(
                    '{project}/charters/revision',
                    [
                        ProjectCharterController::class,
                        'revision',
                    ]
                )
                    ->middleware(
                        'permission:projects.charter.manage'
                    )
                    ->name(
                        'charters.revision'
                    );

                Route::post(
                    '{project}/phases',
                    [
                        ProjectPhaseController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:projects.phases.manage'
                    )
                    ->name(
                        'phases.store'
                    );

                Route::delete(
                    '{project}/phases/{phase}',
                    [
                        ProjectPhaseController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:projects.phases.manage'
                    )
                    ->name(
                        'phases.destroy'
                    );

                Route::post(
                    '{project}/milestones',
                    [
                        ProjectMilestoneController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:projects.milestones.manage'
                    )
                    ->name(
                        'milestones.store'
                    );

                Route::post(
                    '{project}/milestones/{milestone}/complete',
                    [
                        ProjectMilestoneController::class,
                        'complete',
                    ]
                )
                    ->middleware(
                        'permission:projects.milestones.manage'
                    )
                    ->name(
                        'milestones.complete'
                    );

                Route::post(
                    '{project}/stakeholders',
                    [
                        ProjectStakeholderController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:projects.stakeholders.manage'
                    )
                    ->name(
                        'stakeholders.store'
                    );

                Route::post(
                    '{project}/risks',
                    [
                        ProjectRiskController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:projects.risks.manage'
                    )
                    ->name(
                        'risks.store'
                    );

                Route::post(
                    '{project}/risks/{risk}/escalate',
                    [
                        ProjectRiskController::class,
                        'escalate',
                    ]
                )
                    ->middleware(
                        'permission:projects.risks.escalate'
                    )
                    ->name(
                        'risks.escalate'
                    );

                Route::post(
                    '{project}/risks/{risk}/close',
                    [
                        ProjectRiskController::class,
                        'close',
                    ]
                )
                    ->middleware(
                        'permission:projects.risks.manage'
                    )
                    ->name(
                        'risks.close'
                    );

                Route::post(
                    '{project}/decisions',
                    [
                        ProjectDecisionController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:projects.decisions.manage'
                    )
                    ->name(
                        'decisions.store'
                    );
            });

        Route::prefix('projects/{project}/tasks')
            ->name('projects.tasks.')
            ->group(function (): void {
                Route::get(
                    'board',
                    [
                        ProjectTaskController::class,
                        'board',
                    ]
                )
                    ->middleware(
                        'permission:tasks.view'
                    )
                    ->name('board');

                Route::post(
                    '/',
                    [
                        ProjectTaskController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:tasks.create'
                    )
                    ->name('store');

                Route::get(
                    'export/excel',
                    [
                        ProjectTaskController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:tasks.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pdf',
                    [
                        ProjectTaskController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:tasks.export'
                    )
                    ->name('pdf');
            });

        Route::prefix('tasks')
            ->name('tasks.')
            ->group(function (): void {
                Route::get(
                    '{task}',
                    [
                        ProjectTaskController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:tasks.view'
                    )
                    ->name('show');

                Route::put(
                    '{task}',
                    [
                        ProjectTaskController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:tasks.update'
                    )
                    ->name('update');

                Route::delete(
                    '{task}',
                    [
                        ProjectTaskController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:tasks.delete'
                    )
                    ->name('destroy');

                Route::post(
                    '{task}/move',
                    [
                        ProjectTaskWorkflowController::class,
                        'move',
                    ]
                )
                    ->middleware(
                        'permission:tasks.move'
                    )
                    ->name('move');

                Route::post(
                    '{task}/comments',
                    [
                        ProjectTaskCommentController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:tasks.comment'
                    )
                    ->name('comments.store');

                Route::post(
                    '{task}/submissions',
                    [
                        WorkSubmissionController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:work-submissions.create'
                    )
                    ->name('submissions.store');
            });

        Route::prefix('work-submissions')
            ->name('work-submissions.')
            ->group(function (): void {
                Route::post(
                    '{submission}/review',
                    [
                        WorkSubmissionController::class,
                        'review',
                    ]
                )
                    ->middleware(
                        'permission:work-submissions.review'
                    )
                    ->name('review');

                Route::get(
                    '{submission}/attachments/{attachment}/download',
                    [
                        WorkSubmissionAttachmentController::class,
                        'download',
                    ]
                )
                    ->middleware(
                        'permission:work-submissions.attachments.download'
                    )
                    ->name(
                        'attachments.download'
                    );
            });

        Route::prefix('people')
            ->name('people.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        PeopleController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:people.view'
                    )
                    ->name('index');

                Route::get(
                    '{user}',
                    [
                        PeopleController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:people.view'
                    )
                    ->name('show');

                Route::put(
                    '{user}',
                    [
                        PeopleController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:people.update'
                    )
                    ->name('update');

                Route::put(
                    '{user}/skills',
                    [
                        PeopleController::class,
                        'syncSkills',
                    ]
                )
                    ->middleware(
                        'permission:people.skills.manage'
                    )
                    ->name('skills.sync');
            });

        Route::prefix('skills')
            ->name('skills.')
            ->group(function (): void {
                Route::post(
                    '/',
                    [
                        SkillController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:people.skills.manage'
                    )
                    ->name('store');

                Route::post(
                    '{skill}/toggle',
                    [
                        SkillController::class,
                        'toggle',
                    ]
                )
                    ->middleware(
                        'permission:people.skills.manage'
                    )
                    ->name('toggle');
            });

        Route::prefix(
            'projects/{project}/team'
        )
            ->name('projects.team.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ProjectAssignmentController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:project-assignments.view'
                    )
                    ->name('index');

                Route::post(
                    '/',
                    [
                        ProjectAssignmentController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:project-assignments.create'
                    )
                    ->name('store');

                Route::put(
                    '{assignment}',
                    [
                        ProjectAssignmentController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:project-assignments.update'
                    )
                    ->name('update');

                Route::post(
                    '{assignment}/end',
                    [
                        ProjectAssignmentController::class,
                        'end',
                    ]
                )
                    ->middleware(
                        'permission:project-assignments.end'
                    )
                    ->name('end');

                Route::get(
                    'export/excel',
                    [
                        ProjectAssignmentController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:project-assignments.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pdf',
                    [
                        ProjectAssignmentController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:project-assignments.export'
                    )
                    ->name('pdf');
            });

        Route::prefix('timesheets')
            ->name('timesheets.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        TimesheetController::class,
                        'index',
                    ]
                )
                    ->name('index');

                Route::post(
                    '/',
                    [
                        TimesheetController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:timesheets.create'
                    )
                    ->name('store');

                Route::get(
                    '{timesheet}',
                    [
                        TimesheetController::class,
                        'show',
                    ]
                )
                    ->name('show');

                Route::post(
                    '{timesheet}/entries',
                    [
                        TimesheetEntryController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:timesheets.update'
                    )
                    ->name('entries.store');

                Route::put(
                    '{timesheet}/entries/{entry}',
                    [
                        TimesheetEntryController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:timesheets.update'
                    )
                    ->name('entries.update');

                Route::delete(
                    '{timesheet}/entries/{entry}',
                    [
                        TimesheetEntryController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:timesheets.update'
                    )
                    ->name('entries.destroy');

                Route::post(
                    '{timesheet}/submit',
                    [
                        TimesheetApprovalController::class,
                        'submit',
                    ]
                )
                    ->middleware(
                        'permission:timesheets.submit'
                    )
                    ->name('submit');

                Route::post(
                    '{timesheet}/review',
                    [
                        TimesheetApprovalController::class,
                        'review',
                    ]
                )
                    ->middleware(
                        'permission:timesheets.review'
                    )
                    ->name('review');

                Route::get(
                    '{timesheet}/pdf',
                    [
                        TimesheetController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:timesheets.export'
                    )
                    ->name('pdf');

                Route::get(
                    '{timesheet}/excel',
                    [
                        TimesheetController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:timesheets.export'
                    )
                    ->name('excel');
            });

        Route::prefix(
            'people/{user}/cost-rates'
        )
            ->name('people.cost-rates.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ResourceCostRateController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:internal-costs.view'
                    )
                    ->name('index');

                Route::post(
                    '/',
                    [
                        ResourceCostRateController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:internal-costs.manage-rates'
                    )
                    ->name('store');
            });

        Route::post(
            'cost-rates/{rate}/deactivate',
            [
                ResourceCostRateController::class,
                'deactivate',
            ]
        )
            ->middleware(
                'permission:internal-costs.manage-rates'
            )
            ->name(
                'cost-rates.deactivate'
            );

        Route::prefix(
            'projects/{project}/internal-costs'
        )
            ->name('projects.internal-costs.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ProjectInternalCostController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:internal-costs.view'
                    )
                    ->name('show');

                Route::get(
                    'export/excel',
                    [
                        ProjectInternalCostController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:internal-costs.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pdf',
                    [
                        ProjectInternalCostController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:internal-costs.export'
                    )
                    ->name('pdf');
            });

        Route::prefix('suppliers')
            ->name('suppliers.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        SupplierController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:suppliers.view'
                    )
                    ->name('index');

                Route::get(
                    'create',
                    [
                        SupplierController::class,
                        'create',
                    ]
                )
                    ->middleware(
                        'permission:suppliers.create'
                    )
                    ->name('create');

                Route::post(
                    '/',
                    [
                        SupplierController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:suppliers.create'
                    )
                    ->name('store');

                Route::get(
                    '{supplier}',
                    [
                        SupplierController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:suppliers.view'
                    )
                    ->name('show');

                Route::put(
                    '{supplier}',
                    [
                        SupplierController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:suppliers.update'
                    )
                    ->name('update');

                Route::delete(
                    '{supplier}',
                    [
                        SupplierController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:suppliers.delete'
                    )
                    ->name('destroy');
            });

        Route::prefix(
            'projects/{project}/expenses'
        )
            ->name('projects.expenses.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ProjectExpenseController::class,
                        'index',
                    ]
                )
                    ->name('index');

                Route::post(
                    '/',
                    [
                        ProjectExpenseController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:expenses.create'
                    )
                    ->name('store');

                Route::get(
                    'export/excel',
                    [
                        ProjectExpenseController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:expenses.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pdf',
                    [
                        ProjectExpenseController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:expenses.export'
                    )
                    ->name('pdf');
            });

        Route::prefix('expenses')
            ->name('expenses.')
            ->group(function (): void {
                Route::get(
                    '{expense}',
                    [
                        ProjectExpenseController::class,
                        'show',
                    ]
                )
                    ->name('show');

                Route::put(
                    '{expense}',
                    [
                        ProjectExpenseController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:expenses.update'
                    )
                    ->name('update');

                Route::delete(
                    '{expense}',
                    [
                        ProjectExpenseController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:expenses.delete'
                    )
                    ->name('destroy');

                Route::post(
                    '{expense}/submit',
                    [
                        ProjectExpenseApprovalController::class,
                        'submit',
                    ]
                )
                    ->middleware(
                        'permission:expenses.submit'
                    )
                    ->name('submit');

                Route::post(
                    '{expense}/review',
                    [
                        ProjectExpenseApprovalController::class,
                        'review',
                    ]
                )
                    ->middleware(
                        'permission:expenses.review'
                    )
                    ->name('review');

                Route::post(
                    '{expense}/attachments',
                    [
                        ProjectExpenseAttachmentController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:expenses.attachments.manage'
                    )
                    ->name(
                        'attachments.store'
                    );

                Route::get(
                    '{expense}/attachments/{attachment}/download',
                    [
                        ProjectExpenseAttachmentController::class,
                        'download',
                    ]
                )
                    ->middleware(
                        'permission:expenses.attachments.download'
                    )
                    ->name(
                        'attachments.download'
                    );

                Route::delete(
                    '{expense}/attachments/{attachment}',
                    [
                        ProjectExpenseAttachmentController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:expenses.attachments.manage'
                    )
                    ->name(
                        'attachments.destroy'
                    );
            });

        Route::prefix(
            'projects/{project}/non-labor-costs'
        )
            ->name('projects.non-labor-costs.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ProjectCostController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:non-labor-costs.view'
                    )
                    ->name('show');

                Route::get(
                    'export/excel',
                    [
                        ProjectCostController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:non-labor-costs.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pdf',
                    [
                        ProjectCostController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:non-labor-costs.export'
                    )
                    ->name('pdf');
            });

        Route::prefix('invoices')
            ->name('invoices.')
            ->group(function (): void {
                Route::get(
                    'export/excel',
                    [
                        InvoiceController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:invoices.export'
                    )
                    ->name('excel');

                Route::get(
                    '/',
                    [
                        InvoiceController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:invoices.view'
                    )
                    ->name('index');

                Route::get(
                    'create',
                    [
                        InvoiceController::class,
                        'create',
                    ]
                )
                    ->middleware(
                        'permission:invoices.create'
                    )
                    ->name('create');

                Route::post(
                    '/',
                    [
                        InvoiceController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:invoices.create'
                    )
                    ->name('store');

                Route::get(
                    '{invoice}',
                    [
                        InvoiceController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:invoices.view'
                    )
                    ->name('show');

                Route::put(
                    '{invoice}',
                    [
                        InvoiceController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:invoices.update'
                    )
                    ->name('update');

                Route::delete(
                    '{invoice}',
                    [
                        InvoiceController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:invoices.delete'
                    )
                    ->name('destroy');

                Route::post(
                    '{invoice}/issue',
                    [
                        InvoiceController::class,
                        'issue',
                    ]
                )
                    ->middleware(
                        'permission:invoices.issue'
                    )
                    ->name('issue');

                Route::post(
                    '{invoice}/send',
                    [
                        InvoiceController::class,
                        'send',
                    ]
                )
                    ->middleware(
                        'permission:invoices.send'
                    )
                    ->name('send');

                Route::get(
                    '{invoice}/pdf',
                    [
                        InvoiceController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:invoices.view'
                    )
                    ->name('pdf');

                Route::post(
                    '{invoice}/lines',
                    [
                        InvoiceLineController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:invoices.update'
                    )
                    ->name('lines.store');

                Route::put(
                    '{invoice}/lines/{line}',
                    [
                        InvoiceLineController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:invoices.update'
                    )
                    ->name('lines.update');

                Route::delete(
                    '{invoice}/lines/{line}',
                    [
                        InvoiceLineController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:invoices.update'
                    )
                    ->name('lines.destroy');

                Route::post(
                    '{invoice}/expenses/{expense}/import',
                    [
                        InvoiceController::class,
                        'importExpense',
                    ]
                )
                    ->middleware(
                        'permission:invoices.update'
                    )
                    ->name(
                        'expenses.import'
                    );
            });

        Route::prefix('payments')
            ->name('payments.')
            ->group(function (): void {
                Route::get(
                    'export/excel',
                    [
                        PaymentController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:payments.export'
                    )
                    ->name('excel');

                Route::get(
                    '/',
                    [
                        PaymentController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:payments.view'
                    )
                    ->name('index');

                Route::post(
                    '/',
                    [
                        PaymentController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:payments.create'
                    )
                    ->name('store');

                Route::get(
                    '{payment}',
                    [
                        PaymentController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:payments.view'
                    )
                    ->name('show');

                Route::get(
                    '{payment}/pdf',
                    [
                        PaymentController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:payments.view'
                    )
                    ->name('pdf');

                Route::post(
                    '{payment}/send-receipt',
                    [
                        PaymentController::class,
                        'sendReceipt',
                    ]
                )
                    ->middleware(
                        'permission:payments.receipt.send'
                    )
                    ->name(
                        'send-receipt'
                    );

                Route::post(
                    '{payment}/allocations',
                    [
                        PaymentAllocationController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:payments.allocate'
                    )
                    ->name(
                        'allocations.store'
                    );

                Route::delete(
                    '{payment}/allocations/{allocation}',
                    [
                        PaymentAllocationController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:payments.allocate'
                    )
                    ->name(
                        'allocations.destroy'
                    );
            });

        Route::prefix('receivables')
            ->name('receivables.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ReceivableController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:receivables.view'
                    )
                    ->name('index');

                Route::get(
                    'export/excel',
                    [
                        ReceivableController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:receivables.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pdf',
                    [
                        ReceivableController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:receivables.export'
                    )
                    ->name('pdf');
            });

        Route::prefix(
            'projects/{project}/profitability'
        )
            ->name(
                'projects.profitability.'
            )
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ProjectProfitabilityController::class,
                        'show',
                    ]
                )
                    ->middleware(
                        'permission:project-profitability.view'
                    )
                    ->name('show');

                Route::get(
                    'export/excel',
                    [
                        ProjectProfitabilityController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:project-profitability.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pdf',
                    [
                        ProjectProfitabilityController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:project-profitability.export'
                    )
                    ->name('pdf');

                Route::post(
                    'snapshot',
                    [
                        ProjectFinancialSnapshotController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:project-profitability.snapshot'
                    )
                    ->name('snapshot');

                Route::put(
                    'settings',
                    [
                        ProjectFinanceSettingController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:project-finance-settings.manage'
                    )
                    ->name('settings.update');
            });

        Route::prefix('finance')
            ->name('finance.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        FinanceDashboardController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:finance.dashboard.view'
                    )
                    ->name('dashboard');

                Route::get(
                    'export/excel',
                    [
                        FinanceDashboardController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:finance.dashboard.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pdf',
                    [
                        FinanceDashboardController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:finance.dashboard.export'
                    )
                    ->name('pdf');
            });

        Route::prefix(
            'clients/{client}/portal-users'
        )
            ->name(
                'clients.portal-users.'
            )
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ClientPortalUserController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:client-portal-users.view'
                    )
                    ->name('index');

                Route::post(
                    '/',
                    [
                        ClientPortalUserController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:client-portal-users.create'
                    )
                    ->name('store');

                Route::put(
                    '{portalUser}',
                    [
                        ClientPortalUserController::class,
                        'update',
                    ]
                )
                    ->middleware(
                        'permission:client-portal-users.update'
                    )
                    ->name('update');

                Route::post(
                    '{portalUser}/resend',
                    [
                        ClientPortalUserController::class,
                        'resend',
                    ]
                )
                    ->middleware(
                        'permission:client-portal-users.create'
                    )
                    ->name('resend');

                Route::post(
                    '{portalUser}/revoke',
                    [
                        ClientPortalUserController::class,
                        'revoke',
                    ]
                )
                    ->middleware(
                        'permission:client-portal-users.revoke'
                    )
                    ->name('revoke');
            });

        Route::prefix(
            'projects/{project}/client-approvals'
        )
            ->name(
                'projects.client-approvals.'
            )
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ClientApprovalController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:client-approvals.view'
                    )
                    ->name('index');

                Route::post(
                    '/',
                    [
                        ClientApprovalController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:client-approvals.create'
                    )
                    ->name('store');

                Route::get(
                    'export/excel',
                    [
                        ClientApprovalController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:client-approvals.export'
                    )
                    ->name('excel');

                Route::get(
                    'export/pdf',
                    [
                        ClientApprovalController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:client-approvals.export'
                    )
                    ->name('pdf');

                Route::post(
                    '{approval}/cancel',
                    [
                        ClientApprovalController::class,
                        'cancel',
                    ]
                )
                    ->middleware(
                        'permission:client-approvals.cancel'
                    )
                    ->name('cancel');
            });

        Route::prefix(
            'projects/{project}/portal-documents'
        )
            ->name(
                'projects.portal-documents.'
            )
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ClientPortalDocumentController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:client-portal-documents.view'
                    )
                    ->name('index');

                Route::post(
                    '/',
                    [
                        ClientPortalDocumentController::class,
                        'store',
                    ]
                )
                    ->middleware(
                        'permission:client-portal-documents.manage'
                    )
                    ->name('store');

                Route::get(
                    '{document}/download',
                    [
                        ClientPortalDocumentController::class,
                        'download',
                    ]
                )
                    ->middleware(
                        'permission:client-portal-documents.download'
                    )
                    ->name('download');

                Route::delete(
                    '{document}',
                    [
                        ClientPortalDocumentController::class,
                        'destroy',
                    ]
                )
                    ->middleware(
                        'permission:client-portal-documents.manage'
                    )
                    ->name('destroy');
            });

        Route::prefix('reports')
            ->name('reports.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        ReportCenterController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:reports.view'
                    )
                    ->name('index');

                Route::get(
                    'project-operations/export/excel',
                    [
                        ProjectOperationsReportController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:reports.export'
                    )
                    ->name(
                        'project-operations.excel'
                    );

                Route::get(
                    'project-operations/export/pdf',
                    [
                        ProjectOperationsReportController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:reports.export'
                    )
                    ->name(
                        'project-operations.pdf'
                    );

                Route::get(
                    'runs',
                    [
                        ReportRunController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:report-runs.view'
                    )
                    ->name(
                        'runs.index'
                    );

                Route::get(
                    'runs/export/excel',
                    [
                        ReportRunController::class,
                        'excel',
                    ]
                )
                    ->middleware(
                        'permission:report-runs.export'
                    )
                    ->name(
                        'runs.excel'
                    );

                Route::get(
                    'runs/export/pdf',
                    [
                        ReportRunController::class,
                        'pdf',
                    ]
                )
                    ->middleware(
                        'permission:report-runs.export'
                    )
                    ->name(
                        'runs.pdf'
                    );
            });

        Route::prefix('operations')
            ->name('operations.')
            ->group(function (): void {
                Route::get(
                    '/',
                    [
                        AdminOperationsController::class,
                        'index',
                    ]
                )
                    ->middleware(
                        'permission:admin-operations.view'
                    )
                    ->name('index');

                Route::post(
                    'health-check',
                    [
                        AdminOperationsController::class,
                        'check',
                    ]
                )
                    ->middleware(
                        'permission:admin-operations.health-check'
                    )
                    ->name(
                        'health-check'
                    );
            });
    });
