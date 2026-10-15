<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('leads:send-reminders')
    ->hourly()
    ->withoutOverlapping();

Schedule::command(
    'invoices:mark-overdue'
)->dailyAt('00:10');

Schedule::command(
    'finance:capture-snapshots'
)->dailyAt('00:30');
