# Database Design

## M00 Tables

- users
- roles
- permissions
- model_has_roles
- model_has_permissions
- role_has_permissions
- company_settings
- document_sequences
- activity_log
- notifications
- jobs
- failed_jobs
- cache
- sessions

## Users

Public reference: ULID `public_id`.

Status values:

- active
- inactive
- suspended

## Company Settings

Bank account number and SWIFT code are encrypted at the model layer.

## Document Sequences

Document numbers must be allocated inside a database transaction with
a row lock to prevent duplicate numbers.

## M01 Tables

- lead_stages
- lead_sources
- leads
- lead_contacts
- lead_activities
- clients
- client_contacts

### Conversion Rule

A lead may create only one client.

`clients.source_lead_id` is unique.

Lead contact records are copied to client contacts during conversion
inside one database transaction.

M02 extends client records rather than rebuilding them.

## M02 Tables

Extended:

- clients
- client_contacts

Added:

- client_billing_profiles
- client_documents

### Client Documents

Client documents use the private Laravel `local` disk.

Files are downloaded only through authenticated and permission-
protected controller routes.

### Billing Profiles

A client may hold multiple billing profiles, but only one should be
marked as default.

Payment terms are stored as number of days.

## M03 Tables

- requirements
- requirement_versions
- requirement_approvals
- requirement_attachments
- estimates
- estimate_lines

### Requirement Versioning

A requirement is the stable business record.

Its detailed specification is stored in requirement_versions.

Approved versions are locked.

A revision creates a new version rather than modifying the approved
version.

requirements.approved_version_id identifies the commercial baseline
that M04 may consume.

### Estimates

Estimates belong to a specific requirement version.

Estimate lines contain hours, billing rates and internal cost rates.

Revenue, cost and margin totals are recalculated by the application.

Estimates are internal documents.

## M04 Tables

- proposals
- quotations
- contracts
- contract_documents

### Commercial Source Integrity

Proposals and quotations reference:

- an approved requirement version
- an approved estimate

Commercial documents must not recreate or mutate requirement scope.

Contracts reference the accepted quotation and retain the same
requirement-version and estimate lineage.

### Quotation Amounts

Quotation subtotal originates from the approved estimate commercial
revenue.

Discount is applied before tax.

Total:

subtotal

- discount

* tax

Internal estimate cost and margin must not appear on client
commercial documents.

## M05 Tables

- projects
- project_charters
- project_phases
- project_milestones
- project_stakeholders
- project_risks
- project_decisions

projects.contract_id is unique.

Therefore one signed contract can create only one project.

Project commercial fields are copied from the signed contract and
treated as immutable baseline data.

## M06 Tables

- task_workflow_statuses
- project_tasks
- project_task_dependencies
- project_task_comments
- work_submissions
- work_submission_attachments
- task_transitions

Task numbers are unique and contain the parent project number.

Work submissions are versioned per task.

Task transitions retain workflow audit history.

## M07 Tables

- resource_profiles
- skills
- resource_profile_skill
- project_assignments

A user has at most one resource profile.

A user has at most one assignment record per project.

Historical project assignment records are retained through status and
soft deletes.

## M08 Tables

- resource_cost_rates
- timesheets
- timesheet_entries

Resource cost rates are effective-dated.

Timesheets are unique per user and week.

Timesheet entries retain project, task and project-assignment
references.

Approved entries snapshot hourly cost rate, currency and internal cost.

## M09 Tables

- suppliers
- project_expenses
- project_expense_attachments

Project expenses retain project, supplier, incurred-by and approval
references.

Expense evidence is stored privately.

Approved expenses provide M11 non-labor project cost.

## M10 Tables

- invoices
- invoice_lines
- payments
- payment_allocations

Invoices snapshot client billing information.

Invoice lines snapshot quantity, price, tax and total.

Payment allocations connect client payments to invoices.

Invoice amount_paid and balance_due are maintained when allocations are
created or removed.

## M11 Tables

- project_finance_settings
- project_financial_snapshots

project_finance_settings stores project-specific target/warning margins
and alert configuration.

project_financial_snapshots preserves daily profitability and cashflow
metrics.

M11 does not duplicate M08/M09/M10 source transactions.

## M12 Tables

- client_portal_users
- client_password_reset_tokens
- client_approval_requests
- client_portal_documents

Client portal users use a separate authentication provider and session
guard.

Client approval requests retain a safe client-facing summary and
optional internal source reference.

Portal documents are explicit publication records backed by private
storage.

## M13 Tables

### report_runs

Stores audit metadata for reports generated by the M13 reporting layer.

Fields include:

- report type
- format
- completion status
- optional filters
- download filename
- row count
- failure message
- generated by
- generated at

Generated report files are not permanently stored by M13.

## M14 Tables

- support_tickets
- support_ticket_messages
- change_requests
- change_request_estimates

Support ticket messages identify whether the author is internal or a
client portal user.

internal_only support messages are never client-visible.

Change request estimates store both client-facing commercial values and
internal estimate values. Internal fields must not be serialized to the
portal.

## M15 Tables

- employee_profiles
- employee_compensations
- leave_types
- leave_balances
- leave_requests
- attendance_records
- payroll_periods
- payroll_entries
- payroll_adjustments

Employee compensation is kept in a dedicated confidential table.

Payroll entries contain salary snapshots so historical payslips do not
change after compensation updates.

## M16 Database Design

### procurement_requests

Purpose:
Stores procurement request headers.

Key columns:

- id
- public_id
- request_number
- project_id nullable
- title
- business_reason
- priority
- status
- currency
- estimated_total
- requested_by
- submitted_at
- reviewed_by
- review_notes
- reviewed_at
- approved_at
- rejected_at
- timestamps
- soft deletes

Statuses:

- draft
- submitted
- approved
- rejected
- cancelled
- ordered
- partially_received
- received

### procurement_request_items

Purpose:
Stores requested procurement line items.

Key columns:

- id
- public_id
- procurement_request_id
- description
- quantity
- unit
- estimated_unit_cost
- estimated_total
- notes
- timestamps

### purchase_orders

Purpose:
Stores supplier purchase-order headers.

Key columns:

- id
- public_id
- purchase_order_number
- procurement_request_id nullable
- supplier_id
- project_id nullable
- currency
- order_date
- expected_delivery_date
- status
- subtotal
- tax_amount
- total
- supplier_reference
- notes
- created_by
- issued_by
- issued_at
- cancelled_by
- cancelled_at
- timestamps
- soft deletes

Statuses:

- draft
- issued
- partially_received
- received
- cancelled

### purchase_order_items

Purpose:
Stores purchase-order lines.

Key columns:

- id
- public_id
- purchase_order_id
- procurement_request_item_id nullable
- description
- quantity
- unit
- unit_cost
- line_total
- received_quantity
- asset_trackable
- timestamps

### goods_receipts

Purpose:
Stores supplier delivery receipt headers.

Key columns:

- id
- public_id
- receipt_number
- purchase_order_id
- received_date
- delivery_reference
- notes
- received_by
- timestamps

### goods_receipt_items

Purpose:
Stores received quantities per purchase-order line.

Key columns:

- id
- public_id
- goods_receipt_id
- purchase_order_item_id
- quantity_received
- quantity_rejected
- condition_notes
- timestamps

### assets

Purpose:
Stores company-owned tracked assets.

Key columns:

- id
- public_id
- asset_number
- asset_tag
- name
- category
- brand
- model
- serial_number
- supplier_id nullable
- purchase_order_item_id nullable
- project_id nullable
- currency nullable
- purchase_cost nullable
- purchase_date nullable
- warranty_expires_at nullable
- status
- location nullable
- notes nullable
- created_by
- retired_at nullable
- retired_by nullable
- retirement_reason nullable
- timestamps
- soft deletes

Statuses:

- available
- assigned
- maintenance
- lost
- retired
- disposed

### asset_assignments

Purpose:
Stores historical asset assignment records.

Key columns:

- id
- public_id
- asset_id
- user_id
- project_id nullable
- assigned_date
- expected_return_date nullable
- returned_date nullable
- status
- assignment_notes nullable
- return_notes nullable
- assigned_by
- returned_by nullable
- timestamps

Statuses:

- active
- returned

Historical assignment rows are retained and must not be overwritten when an asset is reassigned.

### asset_maintenances

Purpose:
Stores asset maintenance lifecycle records.

Key columns:

- id
- public_id
- asset_id
- type
- status
- started_date nullable
- completed_date nullable
- vendor nullable
- currency nullable
- cost nullable
- description
- resolution nullable
- created_by
- completed_by nullable
- timestamps

Types:

- inspection
- repair
- service
- upgrade

Statuses:

- scheduled
- in_progress
- completed
- cancelled
