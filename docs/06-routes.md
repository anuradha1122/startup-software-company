# Routes

This document tracks the application routes module-by-module.

> Route parameters such as `{project}`, `{task}`, `{expense}`, and `{timesheet}` use Laravel route model binding. Models using `public_id` expose that value through `getRouteKeyName()`.

## M00 – Foundation & Security

| Method | URI                        | Permission / Access       |
| ------ | -------------------------- | ------------------------- |
| GET    | `/admin/users`             | `users.view`              |
| POST   | `/admin/users`             | `users.create`            |
| GET    | `/admin/users/{user}/edit` | `users.update`            |
| PUT    | `/admin/users/{user}`      | `users.update`            |
| DELETE | `/admin/users/{user}`      | `users.delete`            |
| GET    | `/admin/users/export`      | `users.export`            |
| GET    | `/admin/roles`             | `roles.manage`            |
| POST   | `/admin/roles`             | `roles.manage`            |
| PUT    | `/admin/roles/{role}`      | `roles.manage`            |
| DELETE | `/admin/roles/{role}`      | `roles.manage`            |
| GET    | `/admin/company`           | `company-settings.view`   |
| PUT    | `/admin/company`           | `company-settings.update` |
| GET    | `/admin/audit`             | `audit.view`              |
| GET    | `/admin/audit/excel`       | `audit.export`            |
| GET    | `/admin/audit/pdf`         | `audit.export`            |
| GET    | `/notifications`           | Authenticated user        |

## M01 – CRM & Leads

| Method | URI                                | Permission      |
| ------ | ---------------------------------- | --------------- |
| GET    | `/admin/leads`                     | `leads.view`    |
| GET    | `/admin/leads/create`              | `leads.create`  |
| POST   | `/admin/leads`                     | `leads.create`  |
| GET    | `/admin/leads/{lead}`              | `leads.view`    |
| GET    | `/admin/leads/{lead}/edit`         | `leads.update`  |
| PUT    | `/admin/leads/{lead}`              | `leads.update`  |
| PATCH  | `/admin/leads/{lead}/stage`        | `leads.update`  |
| DELETE | `/admin/leads/{lead}`              | `leads.delete`  |
| POST   | `/admin/leads/{lead}/activities`   | `leads.update`  |
| POST   | `/admin/leads/{lead}/convert`      | `leads.convert` |
| GET    | `/admin/leads/export/excel`        | `leads.export`  |
| GET    | `/admin/leads/export/pipeline-pdf` | `leads.export`  |

## M02 – Clients & Contacts

| Method | URI                                                         | Permission                   |
| ------ | ----------------------------------------------------------- | ---------------------------- |
| GET    | `/admin/clients/export/excel`                               | `clients.export`             |
| GET    | `/admin/clients`                                            | `clients.view`               |
| GET    | `/admin/clients/create`                                     | `clients.create`             |
| POST   | `/admin/clients`                                            | `clients.create`             |
| GET    | `/admin/clients/{client}`                                   | `clients.view`               |
| GET    | `/admin/clients/{client}/edit`                              | `clients.update`             |
| PUT    | `/admin/clients/{client}`                                   | `clients.update`             |
| DELETE | `/admin/clients/{client}`                                   | `clients.delete`             |
| GET    | `/admin/clients/{client}/profile-pdf`                       | `clients.view`               |
| POST   | `/admin/clients/{client}/contacts`                          | `clients.contacts.manage`    |
| PUT    | `/admin/clients/{client}/contacts/{contact}`                | `clients.contacts.manage`    |
| DELETE | `/admin/clients/{client}/contacts/{contact}`                | `clients.contacts.manage`    |
| POST   | `/admin/clients/{client}/billing-profiles`                  | `clients.billing.manage`     |
| PUT    | `/admin/clients/{client}/billing-profiles/{billingProfile}` | `clients.billing.manage`     |
| DELETE | `/admin/clients/{client}/billing-profiles/{billingProfile}` | `clients.billing.manage`     |
| POST   | `/admin/clients/{client}/documents`                         | `clients.documents.manage`   |
| GET    | `/admin/clients/{client}/documents/{document}/download`     | `clients.documents.download` |
| DELETE | `/admin/clients/{client}/documents/{document}`              | `clients.documents.manage`   |

## M03 – Requirements & Estimation

| Method | URI                                                                                      | Permission                          |
| ------ | ---------------------------------------------------------------------------------------- | ----------------------------------- |
| GET    | `/admin/requirements`                                                                    | `requirements.view`                 |
| GET    | `/admin/requirements/create`                                                             | `requirements.create`               |
| POST   | `/admin/requirements`                                                                    | `requirements.create`               |
| GET    | `/admin/requirements/{requirement}`                                                      | `requirements.view`                 |
| GET    | `/admin/requirements/{requirement}/versions/{version}/edit`                              | `requirements.update`               |
| PUT    | `/admin/requirements/{requirement}/versions/{version}`                                   | `requirements.update`               |
| POST   | `/admin/requirements/{requirement}/revisions`                                            | `requirements.update`               |
| POST   | `/admin/requirements/{requirement}/versions/{version}/review`                            | `requirements.review`               |
| POST   | `/admin/requirements/{requirement}/versions/{version}/approval-request`                  | `requirements.request-approval`     |
| POST   | `/admin/requirements/{requirement}/versions/{version}/approvals/{approval}/decision`     | `requirements.record-approval`      |
| GET    | `/admin/requirements/{requirement}/versions/{version}/srs-pdf`                           | `requirements.export`               |
| POST   | `/admin/requirements/{requirement}/versions/{version}/attachments`                       | `requirements.attachments.manage`   |
| GET    | `/admin/requirements/{requirement}/versions/{version}/attachments/{attachment}/download` | `requirements.attachments.download` |
| DELETE | `/admin/requirements/{requirement}/versions/{version}/attachments/{attachment}`          | `requirements.attachments.manage`   |
| GET    | `/admin/requirements/{requirement}/versions/{version}/estimates/create`                  | `estimates.create`                  |
| POST   | `/admin/requirements/{requirement}/versions/{version}/estimates`                         | `estimates.create`                  |
| GET    | `/admin/estimates/{estimate}/edit`                                                       | `estimates.view`                    |
| PUT    | `/admin/estimates/{estimate}`                                                            | `estimates.update`                  |
| POST   | `/admin/estimates/{estimate}/approve`                                                    | `estimates.approve`                 |
| GET    | `/admin/estimates/{estimate}/excel`                                                      | `estimates.export`                  |
| GET    | `/admin/estimates/{estimate}/pdf`                                                        | `estimates.export`                  |

## M04 – Proposal, Quotation & Contract

### Proposals

| Method | URI                                    | Permission                  |
| ------ | -------------------------------------- | --------------------------- |
| GET    | `/admin/proposals`                     | `proposals.view`            |
| GET    | `/admin/proposals/create`              | `proposals.create`          |
| POST   | `/admin/proposals`                     | `proposals.create`          |
| GET    | `/admin/proposals/{proposal}`          | `proposals.view`            |
| PUT    | `/admin/proposals/{proposal}`          | `proposals.update`          |
| POST   | `/admin/proposals/{proposal}/approve`  | `proposals.approve`         |
| POST   | `/admin/proposals/{proposal}/send`     | `proposals.send`            |
| POST   | `/admin/proposals/{proposal}/decision` | `proposals.record-decision` |
| GET    | `/admin/proposals/{proposal}/pdf`      | `proposals.export`          |

### Quotations

| Method | URI                                             | Permission                   |
| ------ | ----------------------------------------------- | ---------------------------- |
| GET    | `/admin/quotations`                             | `quotations.view`            |
| GET    | `/admin/quotations/create`                      | `quotations.create`          |
| POST   | `/admin/quotations`                             | `quotations.create`          |
| GET    | `/admin/quotations/{quotation}`                 | `quotations.view`            |
| PUT    | `/admin/quotations/{quotation}`                 | `quotations.update`          |
| POST   | `/admin/quotations/{quotation}/approve`         | `quotations.approve`         |
| POST   | `/admin/quotations/{quotation}/send`            | `quotations.send`            |
| POST   | `/admin/quotations/{quotation}/decision`        | `quotations.record-decision` |
| GET    | `/admin/quotations/{quotation}/excel`           | `quotations.export`          |
| GET    | `/admin/quotations/{quotation}/pdf`             | `quotations.export`          |
| GET    | `/admin/quotations/{quotation}/contract/create` | `contracts.create`           |

### Contracts

| Method | URI                                                         | Permission                     |
| ------ | ----------------------------------------------------------- | ------------------------------ |
| GET    | `/admin/contracts`                                          | `contracts.view`               |
| POST   | `/admin/contracts`                                          | `contracts.create`             |
| GET    | `/admin/contracts/{contract}`                               | `contracts.view`               |
| POST   | `/admin/contracts/{contract}/issue`                         | `contracts.issue`              |
| POST   | `/admin/contracts/{contract}/signed`                        | `contracts.mark-signed`        |
| GET    | `/admin/contracts/{contract}/pdf`                           | `contracts.export`             |
| POST   | `/admin/contracts/{contract}/documents`                     | `contracts.documents.manage`   |
| GET    | `/admin/contracts/{contract}/documents/{document}/download` | `contracts.documents.download` |

## M05 – Project Setup & Governance

| Method | URI                                                         | Permission                     |
| ------ | ----------------------------------------------------------- | ------------------------------ |
| GET    | `/admin/projects/export/excel`                              | `projects.export`              |
| GET    | `/admin/projects`                                           | `projects.view`                |
| GET    | `/admin/projects/create`                                    | `projects.create`              |
| POST   | `/admin/projects`                                           | `projects.create`              |
| GET    | `/admin/projects/{project}`                                 | `projects.view`                |
| PUT    | `/admin/projects/{project}`                                 | `projects.update`              |
| POST   | `/admin/projects/{project}/activate`                        | `projects.activate`            |
| POST   | `/admin/projects/{project}/hold`                            | `projects.update`              |
| POST   | `/admin/projects/{project}/resume`                          | `projects.update`              |
| POST   | `/admin/projects/{project}/complete`                        | `projects.complete`            |
| POST   | `/admin/projects/{project}/kickoff`                         | `projects.kickoff.send`        |
| GET    | `/admin/projects/{project}/charter-pdf`                     | `projects.export`              |
| GET    | `/admin/projects/{project}/charters/{charter}/edit`         | `projects.charter.manage`      |
| PUT    | `/admin/projects/{project}/charters/{charter}`              | `projects.charter.manage`      |
| POST   | `/admin/projects/{project}/charters/{charter}/approve`      | `projects.charter.approve`     |
| POST   | `/admin/projects/{project}/charters/revision`               | `projects.charter.manage`      |
| POST   | `/admin/projects/{project}/phases`                          | `projects.phases.manage`       |
| DELETE | `/admin/projects/{project}/phases/{phase}`                  | `projects.phases.manage`       |
| POST   | `/admin/projects/{project}/milestones`                      | `projects.milestones.manage`   |
| POST   | `/admin/projects/{project}/milestones/{milestone}/complete` | `projects.milestones.manage`   |
| POST   | `/admin/projects/{project}/stakeholders`                    | `projects.stakeholders.manage` |
| POST   | `/admin/projects/{project}/risks`                           | `projects.risks.manage`        |
| POST   | `/admin/projects/{project}/risks/{risk}/escalate`           | `projects.risks.escalate`      |
| POST   | `/admin/projects/{project}/risks/{risk}/close`              | `projects.risks.manage`        |
| POST   | `/admin/projects/{project}/decisions`                       | `projects.decisions.manage`    |

## M06 – Tasks, Workflow & Work Submission

### Project Task Board & Exports

| Method | URI                                            | Permission     |
| ------ | ---------------------------------------------- | -------------- |
| GET    | `/admin/projects/{project}/tasks/board`        | `tasks.view`   |
| POST   | `/admin/projects/{project}/tasks`              | `tasks.create` |
| GET    | `/admin/projects/{project}/tasks/export/excel` | `tasks.export` |
| GET    | `/admin/projects/{project}/tasks/export/pdf`   | `tasks.export` |

### Task Operations

| Method | URI                               | Permission                |
| ------ | --------------------------------- | ------------------------- |
| GET    | `/admin/tasks/{task}`             | `tasks.view`              |
| PUT    | `/admin/tasks/{task}`             | `tasks.update`            |
| DELETE | `/admin/tasks/{task}`             | `tasks.delete`            |
| POST   | `/admin/tasks/{task}/move`        | `tasks.move`              |
| POST   | `/admin/tasks/{task}/comments`    | `tasks.comment`           |
| POST   | `/admin/tasks/{task}/submissions` | `work-submissions.create` |

### Work Submissions

| Method | URI                                                                      | Permission                              |
| ------ | ------------------------------------------------------------------------ | --------------------------------------- |
| POST   | `/admin/work-submissions/{submission}/review`                            | `work-submissions.review`               |
| GET    | `/admin/work-submissions/{submission}/attachments/{attachment}/download` | `work-submissions.attachments.download` |

## M07 – People & Assignments

### People

| Method | URI                           | Permission             |
| ------ | ----------------------------- | ---------------------- |
| GET    | `/admin/people`               | `people.view`          |
| GET    | `/admin/people/{user}`        | `people.view`          |
| PUT    | `/admin/people/{user}`        | `people.update`        |
| PUT    | `/admin/people/{user}/skills` | `people.skills.manage` |

### Skills

| Method | URI                            | Permission             |
| ------ | ------------------------------ | ---------------------- |
| POST   | `/admin/skills`                | `people.skills.manage` |
| POST   | `/admin/skills/{skill}/toggle` | `people.skills.manage` |

### Project Team Assignments

| Method | URI                                               | Permission                   |
| ------ | ------------------------------------------------- | ---------------------------- |
| GET    | `/admin/projects/{project}/team`                  | `project-assignments.view`   |
| POST   | `/admin/projects/{project}/team`                  | `project-assignments.create` |
| GET    | `/admin/projects/{project}/team/export/excel`     | `project-assignments.export` |
| GET    | `/admin/projects/{project}/team/export/pdf`       | `project-assignments.export` |
| PUT    | `/admin/projects/{project}/team/{assignment}`     | `project-assignments.update` |
| POST   | `/admin/projects/{project}/team/{assignment}/end` | `project-assignments.end`    |

## M08 – Timesheets & Internal Costing

### Timesheets

| Method | URI                                             | Permission / Authorization                     |
| ------ | ----------------------------------------------- | ---------------------------------------------- |
| GET    | `/admin/timesheets`                             | `timesheets.view-own` or `timesheets.view-all` |
| POST   | `/admin/timesheets`                             | `timesheets.create`                            |
| GET    | `/admin/timesheets/{timesheet}`                 | Timesheet policy: own or all                   |
| POST   | `/admin/timesheets/{timesheet}/entries`         | `timesheets.update` + Timesheet policy         |
| PUT    | `/admin/timesheets/{timesheet}/entries/{entry}` | `timesheets.update` + Timesheet policy         |
| DELETE | `/admin/timesheets/{timesheet}/entries/{entry}` | `timesheets.update` + Timesheet policy         |
| POST   | `/admin/timesheets/{timesheet}/submit`          | `timesheets.submit` + Timesheet policy         |
| POST   | `/admin/timesheets/{timesheet}/review`          | `timesheets.review`                            |
| GET    | `/admin/timesheets/{timesheet}/pdf`             | `timesheets.export` + Timesheet policy         |
| GET    | `/admin/timesheets/{timesheet}/excel`           | `timesheets.export` + Timesheet policy         |

### Resource Internal Cost Rates

| Method | URI                                   | Permission                    |
| ------ | ------------------------------------- | ----------------------------- |
| GET    | `/admin/people/{user}/cost-rates`     | `internal-costs.view`         |
| POST   | `/admin/people/{user}/cost-rates`     | `internal-costs.manage-rates` |
| POST   | `/admin/cost-rates/{rate}/deactivate` | `internal-costs.manage-rates` |

### Project Internal Labor Cost

| Method | URI                                                     | Permission              |
| ------ | ------------------------------------------------------- | ----------------------- |
| GET    | `/admin/projects/{project}/internal-costs`              | `internal-costs.view`   |
| GET    | `/admin/projects/{project}/internal-costs/export/excel` | `internal-costs.export` |
| GET    | `/admin/projects/{project}/internal-costs/export/pdf`   | `internal-costs.export` |

## M09 – Expenses & Supplier Costs

### Suppliers

| Method | URI                           | Permission         |
| ------ | ----------------------------- | ------------------ |
| GET    | `/admin/suppliers`            | `suppliers.view`   |
| GET    | `/admin/suppliers/create`     | `suppliers.create` |
| POST   | `/admin/suppliers`            | `suppliers.create` |
| GET    | `/admin/suppliers/{supplier}` | `suppliers.view`   |
| PUT    | `/admin/suppliers/{supplier}` | `suppliers.update` |
| DELETE | `/admin/suppliers/{supplier}` | `suppliers.delete` |

### Project Expenses

| Method | URI                                               | Permission / Authorization                 |
| ------ | ------------------------------------------------- | ------------------------------------------ |
| GET    | `/admin/projects/{project}/expenses`              | `expenses.view-own` or `expenses.view-all` |
| POST   | `/admin/projects/{project}/expenses`              | `expenses.create`                          |
| GET    | `/admin/projects/{project}/expenses/export/excel` | `expenses.export`                          |
| GET    | `/admin/projects/{project}/expenses/export/pdf`   | `expenses.export`                          |

### Expense Operations

| Method | URI                                                           | Permission / Authorization         |
| ------ | ------------------------------------------------------------- | ---------------------------------- |
| GET    | `/admin/expenses/{expense}`                                   | Expense policy: own or all         |
| PUT    | `/admin/expenses/{expense}`                                   | `expenses.update` + Expense policy |
| DELETE | `/admin/expenses/{expense}`                                   | `expenses.delete` + Expense policy |
| POST   | `/admin/expenses/{expense}/submit`                            | `expenses.submit` + Expense policy |
| POST   | `/admin/expenses/{expense}/review`                            | `expenses.review`                  |
| POST   | `/admin/expenses/{expense}/attachments`                       | `expenses.attachments.manage`      |
| GET    | `/admin/expenses/{expense}/attachments/{attachment}/download` | `expenses.attachments.download`    |
| DELETE | `/admin/expenses/{expense}/attachments/{attachment}`          | `expenses.attachments.manage`      |

### Approved Non-Labor Project Cost

| Method | URI                                                      | Permission               |
| ------ | -------------------------------------------------------- | ------------------------ |
| GET    | `/admin/projects/{project}/non-labor-costs`              | `non-labor-costs.view`   |
| GET    | `/admin/projects/{project}/non-labor-costs/export/excel` | `non-labor-costs.export` |
| GET    | `/admin/projects/{project}/non-labor-costs/export/pdf`   | `non-labor-costs.export` |

## Route Documentation Rule for Future Modules

Starting with M10, every module implementation must include a complete replacement-ready section for this `06-routes.md` file containing:

- HTTP method
- full URI
- route parameter names
- permission or authorization rule
- route grouping by module/sub-feature
- export/download routes
- approval/workflow action routes
- client-facing routes when applicable

The documentation must be updated in the same module response rather than deferred to a later cleanup step.

## M10 – Invoices, Payments & Receivables

### Invoices

| Method | URI                               | Permission / Authorization        |
| ------ | --------------------------------- | --------------------------------- |
| GET    | `/admin/invoices/export/excel`    | `invoices.export`                 |
| GET    | `/admin/invoices`                 | `invoices.view`                   |
| GET    | `/admin/invoices/create`          | `invoices.create`                 |
| POST   | `/admin/invoices`                 | `invoices.create`                 |
| GET    | `/admin/invoices/{invoice}`       | `invoices.view`                   |
| PUT    | `/admin/invoices/{invoice}`       | `invoices.update` + draft invoice |
| DELETE | `/admin/invoices/{invoice}`       | `invoices.delete` + draft invoice |
| POST   | `/admin/invoices/{invoice}/issue` | `invoices.issue` + draft invoice  |
| POST   | `/admin/invoices/{invoice}/send`  | `invoices.send`                   |
| GET    | `/admin/invoices/{invoice}/pdf`   | `invoices.view`                   |

### Invoice Lines

| Method | URI                                      | Permission / Authorization        |
| ------ | ---------------------------------------- | --------------------------------- |
| POST   | `/admin/invoices/{invoice}/lines`        | `invoices.update` + draft invoice |
| PUT    | `/admin/invoices/{invoice}/lines/{line}` | `invoices.update` + draft invoice |
| DELETE | `/admin/invoices/{invoice}/lines/{line}` | `invoices.update` + draft invoice |

### M09 Billable Expense Import

| Method | URI                                                   | Permission / Authorization                                        |
| ------ | ----------------------------------------------------- | ----------------------------------------------------------------- |
| POST   | `/admin/invoices/{invoice}/expenses/{expense}/import` | `invoices.update` + draft invoice + approved billable M09 expense |

### Payments

| Method | URI                                      | Permission              |
| ------ | ---------------------------------------- | ----------------------- |
| GET    | `/admin/payments/export/excel`           | `payments.export`       |
| GET    | `/admin/payments`                        | `payments.view`         |
| POST   | `/admin/payments`                        | `payments.create`       |
| GET    | `/admin/payments/{payment}`              | `payments.view`         |
| GET    | `/admin/payments/{payment}/pdf`          | `payments.view`         |
| POST   | `/admin/payments/{payment}/send-receipt` | `payments.receipt.send` |

### Payment Allocations

| Method | URI                                                  | Permission          |
| ------ | ---------------------------------------------------- | ------------------- |
| POST   | `/admin/payments/{payment}/allocations`              | `payments.allocate` |
| DELETE | `/admin/payments/{payment}/allocations/{allocation}` | `payments.allocate` |

### Receivables

| Method | URI                               | Permission           |
| ------ | --------------------------------- | -------------------- |
| GET    | `/admin/receivables`              | `receivables.view`   |
| GET    | `/admin/receivables/export/excel` | `receivables.export` |
| GET    | `/admin/receivables/export/pdf`   | `receivables.export` |

### Scheduled Billing Operations

| Schedule    | Command                 | Purpose                                                          |
| ----------- | ----------------------- | ---------------------------------------------------------------- |
| Daily 00:10 | `invoices:mark-overdue` | Refresh issued/partially-paid invoices and mark overdue balances |

## M11 – Project Profitability & Finance

### Project Profitability

| Method | URI                                                    | Permission / Authorization        |
| ------ | ------------------------------------------------------ | --------------------------------- |
| GET    | `/admin/projects/{project}/profitability`              | `project-profitability.view`      |
| GET    | `/admin/projects/{project}/profitability/export/excel` | `project-profitability.export`    |
| GET    | `/admin/projects/{project}/profitability/export/pdf`   | `project-profitability.export`    |
| POST   | `/admin/projects/{project}/profitability/snapshot`     | `project-profitability.snapshot`  |
| PUT    | `/admin/projects/{project}/profitability/settings`     | `project-finance-settings.manage` |

### Finance Portfolio

| Method | URI                           | Permission                 |
| ------ | ----------------------------- | -------------------------- |
| GET    | `/admin/finance`              | `finance.dashboard.view`   |
| GET    | `/admin/finance/export/excel` | `finance.dashboard.export` |
| GET    | `/admin/finance/export/pdf`   | `finance.dashboard.export` |

### Scheduled Finance Operations

| Schedule    | Command                     | Purpose                                                          |
| ----------- | --------------------------- | ---------------------------------------------------------------- |
| Daily 00:10 | `invoices:mark-overdue`     | Refresh M10 overdue invoice state                                |
| Daily 00:30 | `finance:capture-snapshots` | Capture M11 project finance snapshots and evaluate margin alerts |

## M12 – Client Portal & Approvals

### Internal Client Portal User Administration

| Method | URI                                                        | Permission / Authorization   |
| ------ | ---------------------------------------------------------- | ---------------------------- |
| GET    | `/admin/clients/{client}/portal-users`                     | `client-portal-users.view`   |
| POST   | `/admin/clients/{client}/portal-users`                     | `client-portal-users.create` |
| PUT    | `/admin/clients/{client}/portal-users/{portalUser}`        | `client-portal-users.update` |
| POST   | `/admin/clients/{client}/portal-users/{portalUser}/resend` | `client-portal-users.create` |
| POST   | `/admin/clients/{client}/portal-users/{portalUser}/revoke` | `client-portal-users.revoke` |

### Internal Client Approval Administration

| Method | URI                                                            | Permission / Authorization                   |
| ------ | -------------------------------------------------------------- | -------------------------------------------- |
| GET    | `/admin/projects/{project}/client-approvals`                   | `client-approvals.view`                      |
| POST   | `/admin/projects/{project}/client-approvals`                   | `client-approvals.create`                    |
| GET    | `/admin/projects/{project}/client-approvals/export/excel`      | `client-approvals.export`                    |
| GET    | `/admin/projects/{project}/client-approvals/export/pdf`        | `client-approvals.export`                    |
| POST   | `/admin/projects/{project}/client-approvals/{approval}/cancel` | `client-approvals.cancel` + pending approval |

### Internal Portal Document Publishing

| Method | URI                                                              | Permission / Authorization         |
| ------ | ---------------------------------------------------------------- | ---------------------------------- |
| GET    | `/admin/projects/{project}/portal-documents`                     | `client-portal-documents.view`     |
| POST   | `/admin/projects/{project}/portal-documents`                     | `client-portal-documents.manage`   |
| GET    | `/admin/projects/{project}/portal-documents/{document}/download` | `client-portal-documents.download` |
| DELETE | `/admin/projects/{project}/portal-documents/{document}`          | `client-portal-documents.manage`   |

### Client Portal Authentication

| Method | URI                              | Access                           |
| ------ | -------------------------------- | -------------------------------- |
| GET    | `/portal/login`                  | Guest `client_portal`            |
| POST   | `/portal/login`                  | Guest `client_portal`            |
| GET    | `/portal/forgot-password`        | Guest `client_portal`            |
| POST   | `/portal/forgot-password`        | Guest `client_portal`            |
| GET    | `/portal/reset-password/{token}` | Guest `client_portal`            |
| POST   | `/portal/reset-password`         | Guest `client_portal`            |
| POST   | `/portal/logout`                 | Authenticated active portal user |

### Client Portal Dashboard & Projects

| Method | URI                              | Access                                    |
| ------ | -------------------------------- | ----------------------------------------- |
| GET    | `/portal`                        | Authenticated active portal user          |
| GET    | `/portal/projects/{project}`     | Portal user whose client owns the project |
| GET    | `/portal/projects/{project}/pdf` | Portal user whose client owns the project |

### Client Portal Approvals

| Method | URI                                    | Access                                             |
| ------ | -------------------------------------- | -------------------------------------------------- |
| GET    | `/portal/approvals`                    | Authenticated active portal user                   |
| GET    | `/portal/approvals/{approval}`         | Portal user whose client owns the approval         |
| POST   | `/portal/approvals/{approval}/respond` | Portal user whose client owns the pending approval |

### Client Portal Documents

| Method | URI                                     | Access                                                   |
| ------ | --------------------------------------- | -------------------------------------------------------- |
| GET    | `/portal/documents/{document}/download` | Same-client portal user + published/non-expired document |

### Client Portal Invoices

| Method | URI                              | Access                                                               |
| ------ | -------------------------------- | -------------------------------------------------------------------- |
| GET    | `/portal/invoices`               | Authenticated active portal user                                     |
| GET    | `/portal/invoices/{invoice}/pdf` | Same-client portal user + issued/partially-paid/paid/overdue invoice |
