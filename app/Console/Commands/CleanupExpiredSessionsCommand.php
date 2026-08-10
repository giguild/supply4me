<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CleanupExpiredSessionsCommand extends Command
{
    protected $signature = 'session:cleanup {--hours=24 : Sessions older than this many hours will be deleted}';

    protected $description = 'Clean up expired sessions from the database';

    public function handle(): int
    {
        $hours = $this->option('hours');
        $cutoffTime = now()->subHours($hours);

        $this->info("Cleaning up sessions older than {$hours} hours...");

        $deletedCount = 0;

        if (config('session.driver') === 'database') {
            $deletedCount = DB::table('sessions')
                ->where('last_activity', '<', $cutoffTime->timestamp)
                ->delete();
        }

        $this->info("Successfully cleaned up {$deletedCount} expired sessions.");

        return Command::SUCCESS;
    }
}
