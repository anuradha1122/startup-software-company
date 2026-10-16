# Development Log

## M00 — Foundation & Security

Status: In development

Implemented:

- Laravel 13 React/Inertia foundation
- authentication foundation
- 2FA
- email verification
- account status enforcement
- user management
- role/permission management
- company settings
- encrypted banking fields
- document numbering foundation
- audit logging
- notifications
- Excel exports
- PDF audit export
- M00 automated tests

M00 becomes complete only after the full test gate passes.

## M01 — CRM & Leads

Implemented:

- lead sources and pipeline stages
- lead CRUD
- contacts
- assignment
- Kanban pipeline
- lead activities
- follow-ups
- stale detection
- conversion to client
- Excel export
- pipeline PDF
- queued CRM notifications/emails
- CRM test suite

## M02 — Clients & Contacts

Implemented:

- full client profiles
- client numbering
- ownership
- contacts
- billing contacts
- billing profiles
- payment terms
- private documents
- secure downloads
- client profile PDF
- client list Excel
- queued client/contact notifications
- lead-conversion integration
- M02 automated tests

## M03 — Requirements & Estimation

Implemented:

- versioned requirements
- functional/non-functional scope
- assumptions
- constraints
- acceptance criteria
- review workflow
- client approval request workflow
- approval/revision recording
- immutable approved baselines
- private requirement attachments
- estimation lines
- hours/rates/internal costing
- contingency and margin calculation
- SRS PDF
- estimation Excel/PDF
- queued review/approval/revision notifications
- M03 test suite

## M04 — Proposal, Quotation & Contract

Implemented:

- proposal workflow
- approved requirement/estimate source validation
- proposal approval
- proposal PDF/email
- quotation workflow
- discounts/tax/payment terms
- quotation Excel/PDF
- quotation approval/email
- manual commercial decision recording
- accepted quotation to contract conversion
- contract PDF
- contract issue/sign workflow
- private signed-contract storage
- queued commercial notifications
- M04 automated tests

## M05 — Project Setup & Governance

Implemented:

- signed-contract project setup
- immutable commercial baseline
- project numbering
- project manager/client sponsor
- charter versioning and approval
- project lifecycle
- project health
- phases
- milestones
- governance stakeholders
- risk register and escalation
- governance decisions
- kickoff email
- charter PDF
- project Excel export
- M05 automated tests

## M06 — Tasks, Workflow & Work Submission

Implemented:

- task numbering
- task CRUD
- task priorities/types
- phase/milestone linkage
- parent tasks
- dependencies
- Kanban workflow
- workflow transition history
- task comments
- work submission versioning
- private submission evidence
- approval/changes-required workflow
- task completion gate
- queued task/work notifications
- Excel task export
- task PDF report
- M06 automated tests

## M07 — People & Assignments

Implemented:

- operational people directory
- resource profiles
- skills
- skill proficiency
- project assignments
- assignment lifecycle
- allocation tracking
- project manager auto-assignment
- M06 task-assignee enforcement
- assignment notifications
- project team PDF
- assignment Excel
- M07 automated tests

## M08 — Timesheets & Internal Costing

Implemented:

- weekly timesheets
- task-level time entries
- assignment/date validation
- daily hour validation
- timesheet workflow
- effective-dated internal cost rates
- approval-time cost snapshots
- project internal cost reporting
- timesheet PDF/Excel
- internal cost PDF/Excel
- queued timesheet notifications
- M08 automated tests

## M09 — Expenses & Supplier Costs

Implemented:

- supplier directory
- supplier numbering
- project expenses
- expense numbering
- expense tax/total calculation
- private expense evidence
- submission/review workflow
- approved non-labor project cost
- expense PDF/Excel
- non-labor cost PDF/Excel
- queued expense notifications
- M09 automated tests

## M10 — Invoices, Payments & Receivables

Implemented:

- invoice numbering
- draft invoice workflow
- invoice lines/tax calculation
- explicit M09 billable expense import
- invoice issuance
- invoice PDF/email
- payment recording
- receipt numbering
- payment allocations
- partial/full payment status
- receipt PDF/email
- receivable reporting
- overdue invoice scheduler
- Excel exports
- M10 automated tests

## M11 — Project Profitability & Finance

Implemented:

- project profitability engine
- contracted revenue
- invoiced revenue
- collected cash
- M08 labor cost integration
- M09 non-labor cost integration
- M10 billing/payment integration
- gross profit/margin
- unbilled revenue
- receivables
- collection percentage
- currency-integrity validation
- daily financial snapshots
- configurable margin thresholds
- margin warning notifications/email
- profitability PDF/Excel
- finance portfolio PDF/Excel
- M11 automated tests

## M12 — Client Portal & Approvals

Implemented:

- separate client portal authentication
- invitation/password setup
- portal user suspension/revocation
- client dashboard
- safe project view
- client-visible task filtering
- published portal documents
- portal invoice access
- client approval requests
- approval/rejection workflow
- approval emails/notifications
- approval PDF/Excel
- client project summary PDF
- client ownership isolation tests

## M13 — Reports, Exports & Admin Operations

Implemented:

- centralized report center
- cross-module project operations report
- project operations PDF/Excel
- report run auditing
- report run history PDF/Excel
- admin operations dashboard
- database health check
- queue failure visibility
- overdue invoice visibility
- overdue client approval visibility
- finance snapshot health
- manual operational health checks
- scheduled health checking
- queued administrator health alerts

## M14 — Support & Change Requests

Implemented:

- support ticket numbering
- internal support workflow
- client portal support
- ticket assignment
- ticket conversations
- confidential internal support notes
- SLA deadlines
- SLA checker
- change request numbering
- internal and portal change requests
- change estimates
- confidential internal change costing
- approval/rejection workflow
- portal decision notifications
- M06 delivery-task conversion
- support/change exports

## M15 — HR, Leave, Attendance & Payroll

Implemented:

- employee profiles
- confidential compensation
- leave types/balances
- leave request workflow
- leave self-approval protection
- attendance clock-in/out
- payroll periods
- payroll calculations
- decimal-safe payroll money handling
- payroll approval/publication
- employee payslips
- HR notifications
- M15 exports
