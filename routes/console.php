<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reminders:dispatch')->dailyAt('08:00');
Schedule::command('files:purge-quarantine')->daily();

// Learning Center — Competition lifecycle
Schedule::command('learning:open-competitions')->everyMinute();
Schedule::command('learning:close-competitions')->everyMinute();

// Learning Center — Percentile scoring (runs nightly, catches any new attempts)
Schedule::command('learning:compute-percentiles')->dailyAt('02:00');
