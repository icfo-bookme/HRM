<?php

namespace Modules\Loan\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Loan\Models\LoanInstallment;

class CheckOverdueInstallments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'loan:check-overdue';

    /**
     * The console command description.
     */
    protected $description = 'Mark loan installments as Overdue if their due date has passed and they are still Pending';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for overdue loan installments...');

        $count = LoanInstallment::where('status', 'Pending')
            ->where('due_date', '<', Carbon::today())
            ->update(['status' => 'Overdue']);

        $this->info("Done. {$count} installment(s) marked as Overdue.");

        return self::SUCCESS;
    }
}