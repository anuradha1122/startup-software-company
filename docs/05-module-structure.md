# Module Structure

## M00

Backend:

- app/Models
- app/Http/Controllers/Admin
- app/Http/Requests/Admin
- app/Policies
- app/Notifications
- app/Exports
- app/Services

Frontend:

- resources/js/pages/admin
- resources/js/pages/notifications
- resources/js/components/m00

Routes:

- routes/web.php
- routes/admin.php

## M01

Backend:

- Models/Lead\*
- Models/Client\*
- Controllers/Admin/LeadController
- Controllers/Admin/LeadActivityController
- Requests/Admin/_Lead_
- Services/CRM/LeadConversionService
- Notifications/Lead\*
- Exports/LeadsExport
- Console/Commands/SendLeadReminders

Frontend:

- pages/admin/leads
- components/m01

## M03

Backend:

- Models/Requirement\*
- Models/Estimate\*
- Controllers/Admin/Requirement\*
- Controllers/Admin/EstimateController
- Requests/Admin/_Requirement_
- Requests/Admin/StoreEstimateRequest
- Services/Requirements
- Services/Estimation
- Notifications/Requirement\*
- Exports/EstimateExport

Frontend:

- pages/admin/requirements
- pages/admin/estimates
- components/m03

## M04

Backend:

- Models/Proposal
- Models/Quotation
- Models/Contract
- Models/ContractDocument
- Controllers/Admin/ProposalController
- Controllers/Admin/QuotationController
- Controllers/Admin/ContractController
- Controllers/Admin/ContractDocumentController
- Services/Commercial
- Notifications/Commercial\*
- Exports/QuotationExport

Frontend:

- pages/admin/proposals
- pages/admin/quotations
- pages/admin/contracts
- components/m04

## M05

Backend:

- Models/Project\*
- Controllers/Admin/Project\*
- Requests/Admin/Project\*
- Services/Projects
- Notifications/Project\*
- Exports/ProjectsExport

Frontend:

- pages/admin/projects
- components/m05

PDF:

- pdf/project-charter.blade.php

## M06

Backend:

- Models/ProjectTask
- Models/TaskWorkflowStatus
- Models/ProjectTaskDependency
- Models/ProjectTaskComment
- Models/WorkSubmission
- Models/WorkSubmissionAttachment
- Models/TaskTransition
- Controllers/Admin/ProjectTask\*
- Controllers/Admin/WorkSubmission\*
- Services/Tasks
- Notifications/Task\*
- Notifications/WorkSubmission\*
- Exports/ProjectTasksExport

Frontend:

- pages/admin/projects/tasks
- pages/admin/tasks
- components/m06

PDF:

- pdf/project-tasks.blade.php

## M07

Backend:

- Models/ResourceProfile
- Models/Skill
- Models/ProjectAssignment
- Controllers/Admin/PeopleController
- Controllers/Admin/SkillController
- Controllers/Admin/ProjectAssignmentController
- Services/People
- Notifications/ProjectAssignment\*
- Exports/ProjectAssignmentsExport

Frontend:

- pages/admin/people
- pages/admin/projects/team
- components/m07

PDF:

- pdf/project-team.blade.php

## M08

Backend:

- Models/ResourceCostRate
- Models/Timesheet
- Models/TimesheetEntry
- Controllers/Admin/Timesheet\*
- Controllers/Admin/ResourceCostRateController
- Controllers/Admin/ProjectInternalCostController
- Services/Timesheets
- Notifications/Timesheet\*
- Exports/TimesheetExport
- Exports/ProjectInternalCostExport

Frontend:

- pages/admin/timesheets
- pages/admin/people/cost-rates
- pages/admin/projects/internal-costs
- components/m08

PDF:

- pdf/timesheet.blade.php
- pdf/project-internal-cost.blade.php

## M09

Backend:

- Models/Supplier
- Models/ProjectExpense
- Models/ProjectExpenseAttachment
- Controllers/Admin/SupplierController
- Controllers/Admin/ProjectExpenseController
- Controllers/Admin/ProjectExpenseAttachmentController
- Controllers/Admin/ProjectExpenseApprovalController
- Controllers/Admin/ProjectCostController
- Services/Expenses
- Notifications/ProjectExpense\*
- Exports/ProjectExpensesExport
- Exports/ProjectNonLaborCostExport

Frontend:

- pages/admin/suppliers
- pages/admin/projects/expenses
- pages/admin/projects/non-labor-costs
- components/m09

PDF:

- pdf/project-expenses.blade.php
- pdf/project-non-labor-cost.blade.php

## M10

Backend:

- Models/Invoice
- Models/InvoiceLine
- Models/Payment
- Models/PaymentAllocation
- Controllers/Admin/InvoiceController
- Controllers/Admin/InvoiceLineController
- Controllers/Admin/PaymentController
- Controllers/Admin/PaymentAllocationController
- Controllers/Admin/ReceivableController
- Services/Billing
- Notifications/Invoice\*
- Notifications/Payment\*
- Mail/InvoiceIssuedMail
- Mail/PaymentReceiptMail
- Exports/InvoicesExport
- Exports/PaymentsExport
- Exports/ReceivablesExport
- Console/Commands/MarkOverdueInvoices

Frontend:

- pages/admin/invoices
- pages/admin/payments
- pages/admin/receivables
- components/m10

PDF:

- pdf/invoice.blade.php
- pdf/payment-receipt.blade.php
- pdf/receivables.blade.php

## M11

Backend:

- Models/ProjectFinanceSetting
- Models/ProjectFinancialSnapshot
- Controllers/Admin/ProjectProfitabilityController
- Controllers/Admin/FinanceDashboardController
- Controllers/Admin/ProjectFinanceSettingController
- Controllers/Admin/ProjectFinancialSnapshotController
- Services/Finance/ProjectFinancialResult
- Services/Finance/ProjectProfitabilityService
- Services/Finance/ProjectFinancialSnapshotService
- Services/Finance/FinancePortfolioService
- Services/Finance/ProjectMarginAlertService
- Notifications/ProjectMarginAlertNotification
- Exports/ProjectProfitabilityExport
- Exports/FinancePortfolioExport
- Console/Commands/CaptureProjectFinancialSnapshots

Frontend:

- pages/admin/projects/profitability
- pages/admin/finance
- components/m11

PDF:

- pdf/project-profitability.blade.php
- pdf/finance-portfolio.blade.php

## M12

Backend:

- Models/ClientPortalUser
- Models/ClientApprovalRequest
- Models/ClientPortalDocument
- Controllers/Admin/ClientPortalUserController
- Controllers/Admin/ClientApprovalController
- Controllers/Admin/ClientPortalDocumentController
- Controllers/Portal/AuthController
- Controllers/Portal/PasswordResetController
- Controllers/Portal/DashboardController
- Controllers/Portal/ProjectController
- Controllers/Portal/ApprovalController
- Controllers/Portal/DocumentController
- Controllers/Portal/InvoiceController
- Services/Portal
- Notifications/ClientApproval\*
- Notifications/ClientPortalResetPasswordNotification
- Exports/ClientApprovalsExport

Frontend:

- pages/portal
- pages/admin/clients/portal-users
- pages/admin/projects/client-approvals
- pages/admin/projects/portal-documents
- components/m12

PDF:

- pdf/client-approvals.blade.php
- pdf/client-project-summary.blade.php
- existing M10 pdf/invoice.blade.php reused for portal invoice download

## M13

Backend:

- Models/ReportRun
- Controllers/Admin/ReportCenterController
- Controllers/Admin/ProjectOperationsReportController
- Controllers/Admin/ReportRunController
- Controllers/Admin/AdminOperationsController
- Services/Reports/ReportCatalogService
- Services/Reports/ReportRunService
- Services/Reports/ProjectOperationsReportService
- Services/Operations/OperationalHealthService
- Services/Operations/OperationalHealthAlertService
- Notifications/OperationalHealthAlertNotification
- Exports/ProjectOperationsExport
- Exports/ReportRunsExport
- Console/Commands/CheckOperationalHealth

Frontend:

- pages/admin/reports
- pages/admin/operations
- components/m13

PDF:

- pdf/project-operations.blade.php
- pdf/report-runs.blade.php

## M14

Backend:

- Models/SupportTicket
- Models/SupportTicketMessage
- Models/ChangeRequest
- Models/ChangeRequestEstimate
- Controllers/Admin/SupportTicketController
- Controllers/Admin/SupportTicketMessageController
- Controllers/Admin/ChangeRequestController
- Controllers/Admin/ChangeRequestEstimateController
- Controllers/Portal/SupportTicketController
- Controllers/Portal/SupportTicketMessageController
- Controllers/Portal/ChangeRequestController
- Services/Support
- Services/ChangeRequests
- Notifications/SupportTicket\*
- Notifications/ChangeRequest\*
- Exports/SupportTicketsExport
- Exports/ChangeRequestsExport
- Console/Commands/CheckSupportSla

Frontend:

- pages/admin/support
- pages/admin/change-requests
- pages/portal/support
- pages/portal/change-requests
- components/m14

## M15

Backend:

- Models/EmployeeProfile
- Models/EmployeeCompensation
- Models/LeaveType
- Models/LeaveBalance
- Models/LeaveRequest
- Models/AttendanceRecord
- Models/PayrollPeriod
- Models/PayrollEntry
- Models/PayrollAdjustment
- Services/Hr
- Services/Leave
- Services/Attendance
- Services/Payroll
- Controllers/Admin/\*
- Controllers/Employee/\*
- Notifications/Leave\*
- Notifications/PayrollPublishedNotification
- Exports/EmployeesExport
- Exports/LeaveRequestsExport
- Exports/AttendanceExport
- Exports/PayrollRegisterExport

Frontend:

- pages/admin/hr
- pages/employee/leave
- pages/employee/attendance
- pages/employee/payslips
- components/m15

PDF:

- pdf/payslip.blade.php

## M16 — Procurement & Asset Management

### Backend

app/Models/

- ProcurementRequest.php
- ProcurementRequestItem.php
- PurchaseOrder.php
- PurchaseOrderItem.php
- GoodsReceipt.php
- GoodsReceiptItem.php
- Asset.php
- AssetAssignment.php
- AssetMaintenance.php

app/Http/Controllers/Admin/

- ProcurementRequestController.php
- ProcurementReviewController.php
- PurchaseOrderController.php
- GoodsReceiptController.php
- AssetController.php
- AssetAssignmentController.php
- AssetMaintenanceController.php

app/Http/Requests/Admin/

- StoreProcurementRequestRequest.php
- ReviewProcurementRequestRequest.php
- StorePurchaseOrderRequest.php
- StoreGoodsReceiptRequest.php
- StoreAssetRequest.php
- AssignAssetRequest.php
- ReturnAssetRequest.php
- StoreAssetMaintenanceRequest.php
- CompleteAssetMaintenanceRequest.php
- RetireAssetRequest.php

app/Services/Procurement/

- ProcurementMoneyService.php
- ProcurementQuantityService.php
- ProcurementRequestService.php
- ProcurementReviewService.php
- PurchaseOrderService.php
- PurchaseOrderWorkflowService.php
- GoodsReceiptService.php

app/Services/Assets/

- AssetService.php
- AssetAssignmentService.php
- AssetMaintenanceService.php
- AssetLifecycleService.php

app/Exports/

- ProcurementRequestsExport.php
- PurchaseOrdersExport.php
- AssetsExport.php
- AssetAssignmentsExport.php

app/Notifications/

- ProcurementRequestSubmittedNotification.php
- ProcurementRequestReviewedNotification.php
- PurchaseOrderIssuedNotification.php
- AssetAssignedNotification.php
- AssetReturnedNotification.php

resources/views/pdf/

- purchase-order.blade.php

### Frontend

resources/js/pages/admin/procurement/

- index.tsx
- show.tsx

resources/js/pages/admin/purchase-orders/

- index.tsx
- show.tsx

resources/js/pages/admin/assets/

- index.tsx
- show.tsx

### Tests

tests/Feature/M16/

- Helpers.php
- ProcurementRequestCreationTest.php
- ProcurementRequestSelfApprovalTest.php
- ProcurementRequestRejectionTest.php
- ProcurementRequestApprovalTest.php
- PurchaseOrderCreationTest.php
- PurchaseOrderApprovalGuardTest.php
- PurchaseOrderDuplicateGuardTest.php
- PurchaseOrderIssueTest.php
- PurchaseOrderCancellationGuardTest.php
- GoodsReceiptTest.php
- GoodsReceiptPartialTest.php
- GoodsReceiptOverReceiveTest.php
- AssetCreationTest.php
- AssetAssignmentTest.php
- AssetDuplicateAssignmentTest.php
- AssetReturnTest.php
- AssetRetirementTest.php
- AssetRetirementAssignmentGuardTest.php
- ProcurementMoneyTest.php
- ProcurementPrivacyTest.php
- M16ExportTest.php
