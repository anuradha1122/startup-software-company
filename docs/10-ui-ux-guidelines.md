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
