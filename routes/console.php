<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-attendance-report')
    ->everyFiveMinutes()
    ->weekdays()
    ->between('6:00', '19:00');

Schedule::command('attendance:create-daily')
    ->dailyAt('00:00')
    ->weekdays();

// Tanpa jendela jam. Perintahnya sudah menjaga dirinya sendiri: ia diam sampai jam
// absen yang disetel admin lewat. Memasang jendela kedua di sini berarti ada dua
// tempat yang memutuskan kapan absensi ditutup - dan yang di sini tidak tahu-menahu
// soal jam yang disetel admin. Kalau jam absen ditutup lewat pukul 18:55, perintahnya
// tidak pernah dapat giliran, dan siswa yang bolos tidak pernah tercatat alpha.
Schedule::command('attendance:update-unmarked')
    ->everyFiveMinutes()
    ->weekdays();
