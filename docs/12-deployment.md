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
