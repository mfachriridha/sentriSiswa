<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-attendance-report')
    ->everyFiveMinutes()
    ->between('6:00', '19:00');
