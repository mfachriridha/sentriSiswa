<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-attendance-report')
    ->everyFiveMinutes()
    ->between('6:00', '19:00');

Schedule::command('attendance:create-daily')
    ->dailyAt('00:00');

Schedule::command('attendance:update-unmarked')
    ->everyFiveMinutes()
    ->between('7:00', '19:00');
