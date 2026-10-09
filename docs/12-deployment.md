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
