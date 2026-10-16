# Architecture

## Backend

Laravel modular domain-oriented application.

Business workflows should be placed in service/action classes rather
than large controllers.

## Frontend

React + TypeScript + Inertia.

## Authorization

Laravel policies and Spatie permissions.

## IDs

Internal database keys may use bigint IDs.

Public-facing application references use ULIDs where exposed.

## Background Processing

Queues are mandatory for notifications, mail and future document jobs.

Local/default driver: database.

Production target: Redis/Horizon when infrastructure is available.

## Files

Sensitive documents use private Laravel storage.

Only explicitly public assets such as company branding may use the
public disk.

## M05 Architecture

Commercial baseline:

Signed Contract
→ Project

Project references remain linked to:

- Contract
- Client
- Approved Requirement Version
- Approved Estimate

Project operational data is separated into:

- ProjectCharter
- ProjectPhase
- ProjectMilestone
- ProjectStakeholder
- ProjectRisk
- ProjectDecision

## M06 Architecture

Project execution hierarchy:

Project
→ Phase / Milestone
→ Task
→ Workflow Transition
→ Work Submission
→ Review

Task workflow status is normalized through task_workflow_statuses
instead of hard-coded task status strings.

Work submission versions preserve review history.

## M07 Architecture

User
→ ResourceProfile
→ Skills

Project
→ ProjectAssignment
→ User

M06 task ownership is validated through ProjectAssignment.

ProjectAssignment is the operational project membership boundary.

M15 remains responsible for HR employment, attendance, leave and
payroll.

## M08 Architecture

User
→ ResourceCostRate

User
→ Timesheet
→ TimesheetEntry
→ ProjectAssignment
→ ProjectTask

Only approved timesheet entries contribute to internal project cost.

Cost rate values are snapshotted onto approved entries to preserve
historical profitability calculations.

## M09 Architecture

Supplier
→ ProjectExpense
→ ProjectExpenseAttachment

Project
→ ProjectExpense

Only approved ProjectExpense records contribute to actual project
non-labor cost.

M11 consumes M08 labor cost and M09 approved non-labor cost.

## M10 Architecture

Client
→ Invoice
→ InvoiceLine

M09 ProjectExpense
→ optional explicit InvoiceLine import

Client
→ Payment
→ PaymentAllocation
→ Invoice

Outstanding receivables are derived from issued invoices with positive
balance_due.

M11 consumes M10 revenue/billing information for profitability.

## M11 Architecture

M05 Project
→ Contracted Revenue

M08 Approved Timesheet Entries
→ Labor Cost

M09 Approved Project Expenses
→ Non-Labor Cost

M10 Project Invoices
→ Invoiced Revenue
→ Outstanding Receivable

M10 Payment Allocations
→ Collected Cash

These sources are consolidated by ProjectProfitabilityService.

ProjectFinancialSnapshot stores daily historical financial states.

FinancePortfolioService groups portfolio totals by currency.

## M12 Architecture

Internal User
→ explicit publication / approval request
→ Client Portal Boundary

ClientPortalUser
→ Client
→ Project

ClientApprovalRequest
→ Project
→ Client
→ optional internal source reference

ClientPortalDocument
→ Client
→ optional Project

Portal invoice access consumes M10 issued invoices.

M12 never consumes or exposes M11 profitability values.

## M13 Architecture

M01-M12
→ read-only report aggregation
→ Report Center
→ Excel/PDF
→ ReportRun audit record

Application operational state
→ OperationalHealthService
→ Admin Operations Dashboard

OperationalHealthAlertService
→ daily health check
→ internal notification/email when attention is required

M13 does not mutate source module transactions.

## M14 Architecture

M12 ClientPortalUser
→ SupportTicket
→ SupportTicketMessage

Internal User
→ SupportTicket
→ Assignment / Reply / Workflow

Project
→ ChangeRequest
→ ChangeRequestEstimate
→ Review Decision
→ optional M06 ProjectTask

Client-facing DTOs explicitly exclude internal support notes, internal
change analysis and internal costing.
