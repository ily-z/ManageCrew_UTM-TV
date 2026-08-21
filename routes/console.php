<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('absensi:mark-alpa')->everyThirtyMinutes();
Schedule::command('qr:prune')->dailyAt('03:00');
