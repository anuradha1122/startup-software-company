# UI / UX Guidelines

- React + TypeScript
- Inertia navigation
- Tailwind CSS
- shadcn/ui where appropriate
- responsive layouts
- accessible labels
- clear validation messages
- confirmation for destructive actions
- consistent status badges
- permission-aware navigation
- loading/processing states on forms

## M05 UI

Project pages should visually distinguish:

- lifecycle status
- project health
- commercial baseline
- charter status
- milestones
- open risks

Red/amber/green project health must also include readable text and must
not rely on color alone.

Commercial baseline fields should be displayed read-only.

## M06 UI

Task workflow is displayed as a Kanban-style column board.

Task cards must show:

- task number
- title
- priority
- assignee
- workflow state

Status, priority and completion must not rely on color alone.

Task detail pages show work-submission and workflow history.

## M07 UI

People pages display operational project information only.

Project-team screens should clearly show:

- person
- project role
- allocation
- assignment status
- assignment dates
- billable status

Ended assignments remain visible as history.

M06 assignee selectors must only display currently eligible project
members.

## M08 UI

Timesheets should clearly display:

- week
- project
- task
- work date
- hours
- billable status
- approval status

Employee timesheet screens must not display internal hourly cost.

Internal project cost screens require separate privileged access.

## M09 UI

Expense screens should display:

- expense number
- project
- date
- type
- supplier
- subtotal
- tax
- total
- billable flag
- status

Approval status must not rely on color alone.

Private supporting documents must be presented as authenticated
downloads.

## M10 UI

Invoice pages should clearly show:

- invoice number
- client
- project
- invoice date
- due date
- status
- subtotal
- tax
- total
- paid amount
- balance

Payment pages should show:

- receipt number
- client
- amount
- allocated amount
- unallocated amount
- allocation history

Overdue state must not rely on color alone.

## M11 UI

Project profitability screens should clearly distinguish:

- contracted revenue
- invoiced revenue
- collected cash
- labor cost
- non-labor cost
- actual cost
- gross profit
- gross margin
- receivable
- unbilled revenue

Revenue, billing and cash collection must not be presented as equivalent
concepts.

Currency integrity warnings must be visible and must not rely on color
alone.

Portfolio summaries must remain grouped by currency.

## M12 UI

The portal must clearly distinguish:

- project status
- milestones
- client-visible tasks
- documents
- invoices
- approvals

Approval decisions must require an explicit user action.

Rejection must provide a visible comment field.

Portal pages must not display internal navigation or admin controls.

Approval status must not rely on color alone.

## M13 UI

The Report Center should clearly identify:

- report name
- source module
- description
- available formats

Admin Operations should display:

- overall status
- warning count
- critical count
- queue configuration
- individual health checks
- explanatory messages

Operational status must not rely on color alone.

## M14 UI

Support tickets should clearly display:

- ticket number
- client/project
- priority
- severity
- status
- assignee
- first-response SLA
- resolution SLA
- conversation

Internal notes must be visibly marked inside the internal UI.

Client portal must never display an internal-note indicator because
internal notes must never be included in the portal payload.

Change requests should distinguish:

- requested scope
- client estimate
- internal estimate
- review decision
- delivery conversion

## M15 UI

HR screens should separate:

- employment profile
- compensation
- leave
- attendance
- payroll

Salary values must not appear on ordinary employee-directory screens.

Employee self service should emphasize:

- leave balance
- leave request status
- today's attendance
- published payslips

Payroll workflow must clearly display:

Draft → Calculated → Approved → Published → Closed

## M16 UI/UX Guidelines

M16 pages follow the existing internal admin UI conventions.

### Page Layout

Use:

- `max-w-7xl`
- consistent `p-6`
- `space-y-6`
- bordered sections
- responsive grid layouts
- existing muted text conventions
- simple workflow buttons
- existing project Tailwind tokens

M16 must visually match existing admin pages such as project management pages.

### Procurement UI

Procurement request screens should show:

- request number;
- title;
- priority;
- status;
- requester;
- project;
- currency;
- estimated total;
- line items;
- review status.

Only display actions the current user is allowed to perform.

### Purchase Order UI

Purchase-order screens should show:

- PO number;
- supplier;
- procurement request;
- order date;
- expected delivery date;
- line items;
- ordered quantity;
- received quantity;
- unit price;
- subtotal;
- tax;
- total;
- status;
- goods receipt history.

### Asset UI

Asset screens should show:

- asset number;
- asset tag;
- name;
- category;
- serial number;
- location;
- project;
- status;
- active assignment;
- assignment history;
- maintenance history.

Purchase cost must only be displayed to authorized internal users.

### Action Visibility

Examples:

- Submit button only for eligible drafts.
- Review form only for submitted requests and authorized reviewers.
- Issue button only for draft POs.
- Goods receipt form only for issued/partially-received POs.
- Assign button only for available assets.
- Return form only when an active assignment exists.
- Retirement action disabled while assignment or maintenance blocks retirement.

### Wayfinder

All backend navigation/action URLs in new M16 React code must use generated Wayfinder actions.

Do not introduce hardcoded Laravel endpoint strings into M16 frontend pages.
