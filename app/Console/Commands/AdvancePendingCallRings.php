<?php

namespace App\Console\Commands;

use App\Services\CallRingService;
use Illuminate\Console\Command;

class AdvancePendingCallRings extends Command
{
    protected $signature = 'calls:advance-pending-rings {--once : Run a single pass instead of polling for ~55 seconds}';

    protected $description = 'Release stuck Accepted calls and advance sequential rings past 30s timeout';

    public function handle(): int
    {
        // Single pass (useful for cron / manual runs)
        if ($this->option('once')) {
            $advanced = CallRingService::advanceOverdueCalls();
            $this->info("Processed {$advanced} overdue sequential call(s) (includes stale live-call release).");

            return self::SUCCESS;
        }

        // When launched by schedule:work every minute, poll every 10s so 30s timeouts
        // are handled promptly (Laravel 10.0 has no everyTenSeconds scheduler).
        $deadline = time() + 55;
        $total = 0;

        do {
            $total += CallRingService::advanceOverdueCalls();
            if (time() >= $deadline) {
                break;
            }
            sleep(10);
        } while (time() < $deadline);

        $this->info("Processed {$total} overdue sequential call(s) (includes stale live-call release).");

        return self::SUCCESS;
    }
}
