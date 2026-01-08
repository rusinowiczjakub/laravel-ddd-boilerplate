<?php

use Illuminate\Support\Facades\Schedule;

// Horizon metrics snapshot (every 5 minutes)
Schedule::command('horizon:snapshot')->everyFiveMinutes();
