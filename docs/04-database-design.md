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
