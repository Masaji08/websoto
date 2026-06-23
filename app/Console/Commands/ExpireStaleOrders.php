<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireStaleOrders extends Command
{
    protected $signature = 'orders:expire-stale';

    protected $description = 'Cancel unpaid orders older than 30 minutes';

    public function handle(): int
    {
        $count = Order::whereNotIn('status', ['completed', 'cancelled'])
            ->where('payment_status', 'unpaid')
            ->where('created_at', '<', now()->subMinutes(30))
            ->limit(50)
            ->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);

        if ($count > 0) {
            Log::info("ExpireStaleOrders: cancelled {$count} stale unpaid orders.");
            $this->info("Cancelled {$count} stale unpaid orders.");
        } else {
            $this->info('No stale orders found.');
        }

        return Command::SUCCESS;
    }
}
