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
