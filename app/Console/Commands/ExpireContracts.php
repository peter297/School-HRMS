<?php

namespace App\Console\Commands;

use App\Models\Contract;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:expire-contracts')]
#[Description('Command description')]
class ExpireContracts extends Command
{
    /**
     * Execute the console command.
     */

    protected $signature = 'contracts:expire';
    protected $description = 'Mark contracts whose end date has passed as expired';
    public function handle()
    {
        $count = Contract::where('status', 'active')
            ->whereNotnull('end_date')
            ->whereDate('end_date', '<', today())
            ->update(['status' => 'expired']);

            $this->info("Marked {$count} contract(s) as expired.");
    }
}
