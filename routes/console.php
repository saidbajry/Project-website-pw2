<?php

use Illuminate\Support\Facades\Schedule;

// Automasi pengingat WhatsApp servis berikutnya (jalankan `php artisan schedule:run` tiap menit via cron/Task Scheduler)
Schedule::command('servis:pengingat')->dailyAt('08:00');
