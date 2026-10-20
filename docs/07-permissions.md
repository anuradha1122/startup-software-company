# Roles & Permissions

## Core Roles

- Director
- Admin
- Sales
- Project Manager
- Finance
- HR
- Employee
- Freelancer
- Client

## M00 Permissions

- users.view
- users.create
- users.update
- users.delete
- users.export
- roles.manage
- company-settings.view
- company-settings.update
- audit.view
- audit.export

Director and Admin receive every M00 permission.

Permissions are assigned to roles rather than directly to users.

## M01 Permissions

- leads.view
- leads.create
- leads.update
- leads.delete
- leads.assign
- leads.convert
- leads.export

Director/Admin:
all M01 permissions.

Sales:
view, create, update, assign, convert, export.

Project Manager:
view and export.

## M02 Permissions

- clients.view
- clients.create
- clients.update
- clients.delete
- clients.export
- clients.contacts.manage
- clients.billing.manage
- clients.documents.manage
- clients.documents.download

## M03 Permissions

Requirements:

- requirements.view
- requirements.create
- requirements.update
- requirements.review
- requirements.request-approval
- requirements.record-approval
- requirements.export
- requirements.attachments.manage
- requirements.attachments.download

Estimation:

- estimates.view
- estimates.create
- estimates.update
- estimates.approve
- estimates.export

## M04 Permissions

### Proposals

- proposals.view
- proposals.create
- proposals.update
- proposals.approve
- proposals.send
- proposals.record-decision
- proposals.export

### Quotations

- quotations.view
- quotations.create
- quotations.update
- quotations.approve
- quotations.send
- quotations.record-decision
- quotations.export

### Contracts

- contracts.view
- contracts.create
- contracts.update
- contracts.issue
- contracts.mark-signed
- contracts.export
- contracts.documents.manage
- contracts.documents.download

## M05 Permissions

- projects.view
- projects.create
- projects.update
- projects.activate
- projects.complete
- projects.export
- projects.charter.manage
- projects.charter.approve
- projects.phases.manage
- projects.milestones.manage
- projects.stakeholders.manage
- projects.risks.manage
- projects.risks.escalate
- projects.decisions.manage
- projects.kickoff.send

## M06 Permissions

### Tasks

- tasks.view
- tasks.create
- tasks.update
- tasks.delete
- tasks.assign
- tasks.move
- tasks.comment
- tasks.export

### Work Submissions

- work-submissions.view
- work-submissions.create
- work-submissions.review
- work-submissions.attachments.manage
- work-submissions.attachments.download

## M07 Permissions

### People

- people.view
- people.update
- people.skills.manage
- people.export

### Project Assignments

- project-assignments.view
- project-assignments.create
- project-assignments.update
- project-assignments.end
- project-assignments.export

## M08 Permissions

### Timesheets

- timesheets.view-own
- timesheets.view-all
- timesheets.create
- timesheets.update
- timesheets.submit
- timesheets.review
- timesheets.export

### Internal Costing

- internal-costs.view
- internal-costs.manage-rates
- internal-costs.export

## M09 Permissions

### Suppliers

- suppliers.view
- suppliers.create
- suppliers.update
- suppliers.delete

### Expenses

- expenses.view-own
- expenses.view-all
- expenses.create
- expenses.update
- expenses.delete
- expenses.submit
- expenses.review
- expenses.attachments.manage
- expenses.attachments.download
- expenses.export

### Non-Labor Costs

- non-labor-costs.view
- non-labor-costs.export

## M10 Permissions

### Invoices

- invoices.view
- invoices.create
- invoices.update
- invoices.delete
- invoices.issue
- invoices.send
- invoices.void
- invoices.export

### Payments

- payments.view
- payments.create
- payments.allocate
- payments.export
- payments.receipt.send

### Receivables

- receivables.view
- receivables.export

## M11 Permissions

### Project Profitability

- project-profitability.view
- project-profitability.export
- project-profitability.snapshot

### Finance Settings

- project-finance-settings.manage

### Finance Portfolio

- finance.dashboard.view
- finance.dashboard.export
- finance.alerts.receive

## M12 Permissions

### Portal Users

- client-portal-users.view
- client-portal-users.create
- client-portal-users.update
- client-portal-users.revoke

### Client Approvals

- client-approvals.view
- client-approvals.create
- client-approvals.cancel
- client-approvals.export

### Portal Documents

- client-portal-documents.view
- client-portal-documents.manage
- client-portal-documents.download

Client portal authorization is client-scoped and does not use internal
Spatie roles/permissions.

## M13 Permissions

### Reports

- reports.view
- reports.export

### Report History

- report-runs.view
- report-runs.export

### Admin Operations

- admin-operations.view
- admin-operations.health-check
- admin-operations.alerts.receive

## M14 Permissions

### Support

- support-tickets.view
- support-tickets.create
- support-tickets.update
- support-tickets.assign
- support-tickets.reply
- support-tickets.resolve
- support-tickets.export

### Change Requests

- change-requests.view
- change-requests.create
- change-requests.update
- change-requests.estimate
- change-requests.review
- change-requests.convert-task
- change-requests.export

Client portal access remains client-scoped and does not use internal
Spatie roles.

## M15 Permissions

### Employees

- employees.view
- employees.create
- employees.update
- employees.terminate
- employees.export

### Compensation

- employee-compensation.view
- employee-compensation.manage

### Leave

- leave-types.view
- leave-types.manage
- leave-requests.view-own
- leave-requests.view-all
- leave-requests.create
- leave-requests.review
- leave-requests.cancel
- leave-requests.export

### Attendance

- attendance.view-own
- attendance.view-all
- attendance.clock
- attendance.manage
- attendance.export

### Payroll

- payroll.view
- payroll.calculate
- payroll.adjust
- payroll.approve
- payroll.publish
- payroll.export
- payslips.view-own
- payslips.download-own

## M16 Permissions

### Procurement Requests

- `procurement-requests.view`
- `procurement-requests.create`
- `procurement-requests.update`
- `procurement-requests.submit`
- `procurement-requests.review`
- `procurement-requests.cancel`
- `procurement-requests.export`

### Purchase Orders

- `purchase-orders.view`
- `purchase-orders.create`
- `purchase-orders.update`
- `purchase-orders.issue`
- `purchase-orders.cancel`
- `purchase-orders.export`

### Goods Receipts

- `goods-receipts.view`
- `goods-receipts.create`

### Assets

- `assets.view`
- `assets.create`
- `assets.update`
- `assets.assign`
- `assets.return`
- `assets.maintain`
- `assets.retire`
- `assets.export`

### Asset Assignments

- `asset-assignments.view`
- `asset-assignments.export`

### Permission Rules

Permissions must be added without destroying previously assigned role permissions.

Use additive permission grants.

Do not use broad destructive permission synchronization such as:

php
$role->syncPermissions(
Permission::all()
);
