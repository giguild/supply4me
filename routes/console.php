<?php

use App\Console\Commands\CheckOverdueInvoicesCommand;
use App\Console\Commands\GenerateDailySalesReportCommand;
use App\Console\Commands\ReorderLowStockProductsCommand;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| Scheduler definitions for the Supply4Me application.
|
*/

// ── Daily Tasks ──────────────────────────────────────────────
Schedule::command('invoices:check-overdue')->dailyAt('08:00');
Schedule::command('reports:daily-sales')->dailyAt('23:00');
Schedule::command('inventory:check-reorder')->everySixHours();
Schedule::command('activity:clean')->weekly();
Schedule::command('queue:prune-failed --hours=48')->daily();
Schedule::command('cache:prune-stale-tags')->hourly();
