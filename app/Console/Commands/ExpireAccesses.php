<?php

namespace App\Console\Commands;

use App\Models\UserToolAccess;
use Illuminate\Console\Command;

class ExpireAccesses extends Command
{
    protected $signature = 'access:expire';

    protected $description = 'Mark UserToolAccess records past their expiry as expired (frees account slots).';

    public function handle(): int
    {
        $count = 0;

        UserToolAccess::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->chunkById(100, function ($accesses) use (&$count) {
                foreach ($accesses as $access) {
                    $access->status = 'expired';
                    $access->save();
                    $count++;
                }
            });

        $this->info("Expired {$count} access record(s).");

        return self::SUCCESS;
    }
}
