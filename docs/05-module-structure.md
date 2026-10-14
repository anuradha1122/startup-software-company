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
