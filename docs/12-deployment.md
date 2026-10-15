# Deployment

## Minimum

- PHP 8.4+
- Laravel 13 supported extensions
- MySQL 8+
- Node.js build environment
- queue worker
- scheduler
- HTTPS
- production mail provider
- backups

## Queue

Development:

database queue

Production recommendation:

Redis + Laravel Horizon.

## Scheduler

Production cron:

-   -   -   -   - php /path/to/artisan schedule:run >> /dev/null 2>&1

## M05 Deployment Notes

M05 uses the existing queue worker for:

- project-created notifications
- kickoff emails
- risk-escalation notifications

No public storage link is required for M05.

After deployment regenerate Wayfinder and restart queue workers.

## M06 Deployment Notes

Seed task workflow statuses during deployment.

M06 uses the existing queue worker for:

- task assignment notifications
- work-submission review notifications
- changes-required notifications
- work-approval notifications

Work submission attachments remain on private storage.

No public storage link is required.

## M07 Deployment Notes

M07 uses the existing queue worker for project assignment emails and
database notifications.

No public storage is introduced.

Existing projects should receive a project-manager assignment before
M07 task-assignee enforcement is enabled in a production upgrade.

Fresh installations receive this automatically through project setup.

## M08 Deployment Notes

M08 uses the existing queue worker for timesheet submission,
approval and rejection notifications.

No public file storage is introduced.

Internal cost permissions should be restricted to authorized finance,
management and project-governance roles.

## M09 Deployment Notes

M09 uses the existing queue worker for expense submission, approval and
rejection notifications.

Expense evidence uses private local storage.

No public storage link is required.

M09 requires supplier and expense document sequences.

## M10 Deployment Notes

M10 uses the existing queue worker for invoice and receipt emails.

The scheduler must run Laravel scheduled tasks so overdue invoice status
is refreshed daily.

Required scheduled command:

invoices:mark-overdue

Production cron should continue invoking Laravel schedule:run according
to the application's deployment standard.

## M11 Deployment Notes

M11 uses the existing queue worker for margin warning mail and database
notifications.

Laravel scheduler must execute:

finance:capture-snapshots

daily after the M10 overdue invoice refresh.

Current schedule:

00:10 invoices:mark-overdue
00:30 finance:capture-snapshots

Financial reports require no public storage.

## M12 Deployment Notes

M12 requires:

- client_portal session guard
- client_portal_users auth provider
- client_portal password broker
- queue worker for invitations and approval notifications
- private storage access through authenticated controllers
- HTTPS in production

No public storage link is required for portal documents.

Portal password reset URLs must use the production APP_URL.
