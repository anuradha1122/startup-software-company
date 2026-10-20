# System Scope

## M00 — Foundation & Security

Status: Complete after M00 test gate passes.

Includes:

- Laravel authentication
- email verification
- password reset
- two-factor authentication
- account status control
- users
- roles
- permissions
- company settings
- bank details
- document branding
- document number sequences
- audit logging
- private storage foundation
- queued notifications
- user Excel export
- audit Excel/PDF export
- notifications centre

Remaining modules follow M01–M16 from the approved system plan.

## M01 — CRM & Leads

Status: Complete after M01 acceptance gate.

Includes:

- lead creation and editing
- lead sources
- primary lead contacts
- lead assignment
- Kanban pipeline
- pipeline stage movement
- probability tracking
- lead activities
- follow-up scheduling
- stale lead detection
- conversion to client
- Excel lead export
- pipeline PDF
- assignment notifications
- follow-up due notifications
- stale lead notifications

## M02 — Clients & Contacts

Includes:

- client profiles
- client numbering
- client ownership
- client contacts
- primary contacts
- billing contacts
- decision makers
- billing profiles
- payment terms
- tax information
- private client documents
- secure document downloads
- lead-to-client integration
- client list Excel
- client profile PDF
- client-created notifications
- contact-added notifications

## M03 — Requirements & Estimation

Includes:

- versioned software requirements
- functional requirements
- non-functional requirements
- included/excluded scope
- assumptions and constraints
- acceptance criteria
- client-visible notes
- internal notes
- requirement review workflow
- client approval requests
- approval/revision decisions
- immutable approved versions
- requirement attachments
- estimation worksheets
- estimated hours
- billing rates
- internal cost rates
- contingency
- revenue/cost/margin calculation
- SRS PDF
- estimate Excel/PDF

## M04 — Proposal, Quotation & Contract

Includes:

- proposal generation from approved requirements and estimates
- internal proposal approval
- proposal PDF
- proposal email delivery
- client proposal decision recording
- quotation generation
- discounts and taxes
- validity dates
- payment terms
- payment schedules
- quotation PDF
- quotation Excel
- quotation approval
- quotation email delivery
- quotation acceptance/rejection
- contract creation from accepted quotation
- contract PDF
- contract issue workflow
- signed-contract recording
- private signed-contract storage

## M05 — Project Setup & Governance

Includes:

- signed contract to project conversion
- project numbering
- project manager
- client sponsor
- project lifecycle
- project health
- project charter versioning
- charter approval
- phases
- milestones
- governance stakeholders
- risk register
- risk escalation
- governance decision register
- kickoff email
- project charter PDF
- project Excel export

## M06 — Tasks, Workflow & Work Submission

Includes:

- project task creation
- project task numbering
- task types and priorities
- task assignees
- phase/milestone linkage
- parent/sub-task linkage
- dependencies
- Kanban workflow
- workflow transition history
- task comments
- work submissions
- work submission versioning
- private submission attachments
- review approval
- changes-required workflow
- task completion gate
- task Excel export
- task PDF report
- assignment/review notifications

## M07 — People & Assignments

Includes:

- operational people directory
- resource profiles
- skills catalogue
- resource skills/proficiency
- project team assignments
- project roles
- allocation percentage
- planned/active/ended assignment lifecycle
- billable assignment flag
- project manager auto-assignment
- task assignee eligibility
- project assignment notifications
- project team PDF
- project assignment Excel

## M08 — Timesheets & Internal Costing

Includes:

- weekly timesheets
- task-level time entries
- project-assignment validation
- daily hour validation
- timesheet submission
- approval/rejection
- effective-dated internal hourly cost rates
- historical cost snapshots
- internal project cost reporting
- timesheet PDF
- timesheet Excel
- internal cost PDF
- internal cost Excel
- queued review notifications

## M09 — Expenses & Supplier Costs

Includes:

- supplier directory
- supplier numbering
- project expenses
- supplier-linked costs
- expense categories
- tax and total calculation
- billable-to-client marker
- private receipts and supplier invoices
- submission workflow
- approval/rejection
- expense PDF/Excel
- approved non-labor cost PDF/Excel
- queued approval notifications

## M10 — Invoices, Payments & Receivables

Includes:

- invoice numbering
- draft invoices
- invoice lines
- tax calculation
- approved M09 billable expense import
- invoice issue workflow
- client invoice PDF
- invoice email
- payment recording
- receipt numbering
- payment allocation
- partial payments
- full payment settlement
- payment receipt PDF/email
- outstanding receivables
- overdue detection
- Excel/PDF reports
- queued notifications and emails

## M11 — Project Profitability & Finance

Includes:

- contracted revenue
- invoiced revenue
- collected cash
- approved labor cost
- approved non-labor cost
- actual project cost
- gross profit
- gross margin
- unbilled revenue
- outstanding receivables
- collection percentage
- cost consumption percentage
- invoice progress percentage
- multi-currency integrity checking
- daily financial snapshots
- configurable project margin thresholds
- margin warning notifications/emails
- project profitability PDF/Excel
- finance portfolio PDF/Excel

## M12 — Client Portal & Approvals

Includes:

- separate client portal authentication
- portal invitations/password setup
- client access suspension/revocation
- client dashboard
- client project summaries
- client-visible milestones
- client-visible tasks
- explicit private document publication
- portal invoice access
- client approval requests
- approval/rejection responses
- approval notification emails
- internal response notifications
- approval Excel/PDF exports
- client-safe project summary PDF

## M13 — Reports, Exports & Admin Operations

Includes:

- centralized report center
- cross-module project operations report
- project operations Excel/PDF
- report generation audit history
- report history Excel/PDF
- database health check
- failed queue job visibility
- overdue invoice visibility
- overdue client approval visibility
- finance snapshot health visibility
- manual health-check execution
- scheduled operational health checks
- queued administrator health alerts

## M14 — Support & Change Requests

Includes:

- internal support ticket creation
- client portal support tickets
- ticket priority and severity
- ticket assignment
- support conversations
- internal-only support notes
- first-response SLA
- resolution SLA
- resolution/closure workflow
- project-linked change requests
- client portal change requests
- client-safe change estimates
- internal change costing
- change approval/rejection
- approval notifications
- optional M06 task conversion
- support/change Excel exports

## M15 — HR, Leave, Attendance & Payroll

Includes:

- employee profiles
- employment status/type
- confidential compensation history
- leave types
- leave balances
- employee leave requests
- leave review workflow
- self-approval prevention
- employee attendance clock-in/out
- attendance administration
- payroll periods
- payroll calculation
- payroll adjustments
- payroll approval
- payroll publication
- employee self-service payslips
- payroll PDF/Excel
- leave/attendance/employee exports

## M16 Scope — Procurement & Asset Management

### In Scope

M16 includes:

#### Procurement

- creation of procurement requests;
- optional linkage to a project;
- business justification;
- priority classification;
- multi-line requested items;
- estimated unit cost and estimated total;
- submission workflow;
- internal review;
- approval;
- rejection with reason;
- requester/reviewer separation.

#### Purchase Orders

- conversion of approved procurement requests into purchase orders;
- supplier selection;
- purchase-order numbering;
- actual unit pricing;
- tax amount;
- supplier reference;
- expected delivery date;
- issue workflow;
- cancellation before goods receipt;
- purchase-order PDF;
- purchase-order Excel export.

#### Goods Receipt

- receipt against issued purchase orders;
- partial receipt;
- cumulative received quantity tracking;
- over-receipt prevention;
- rejected quantity recording;
- delivery reference;
- condition notes;
- receiver audit data.

#### Assets

- asset registration;
- asset number and asset tag;
- supplier and project linkage;
- purchase-order-item linkage;
- purchase cost;
- serial number;
- warranty expiry;
- location;
- asset status tracking;
- employee/user assignment;
- assignment history;
- return workflow;
- maintenance records;
- retirement.

### Out of Scope

The following are explicitly outside M16:

- supplier payment processing;
- automatic invoice generation;
- client billing;
- payroll deductions for assets;
- salary processing;
- depreciation accounting;
- general ledger postings;
- automatic M09 expense creation;
- automatic M08 project-cost creation;
- client approvals for procurement;
- client access to supplier pricing;
- stock warehouse management;
- purchase requisition bidding/tender management;
- disposal sale accounting.

### Module Boundaries

M16 procurement and asset values are internal operational records.

M16 purchase costs do not automatically become:

- M08 internal labor cost;
- M09 project expense;
- M10 client invoice;
- M11 profitability revenue;
- M15 payroll deduction.

Any future integration must be explicit, authorized, auditable, and implemented through a dedicated workflow.
