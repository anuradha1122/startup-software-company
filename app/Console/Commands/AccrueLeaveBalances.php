<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:accrue-leave-balances')]
#[Description('Command description')]
class AccrueLeaveBalances extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
