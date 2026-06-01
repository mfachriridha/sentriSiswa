<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-attendance-report')
    ->everyFiveMinutes()
    ->weekdays()
    ->between('6:00', '19:00');

Schedule::command('attendance:create-daily')
    ->dailyAt('00:00')
    ->weekdays();

Schedule::command('attendance:update-unmarked')
    ->everyFiveMinutes()
    ->weekdays()
    ->between('7:00', '19:00');
