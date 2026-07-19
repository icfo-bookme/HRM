<?php

namespace Modules\Kpi\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Kpi\Services\KpiMonthlyService;
use Modules\Employee\Models\Employee;

class CloseMonthlyKpi extends Command
{
    protected $signature = 'kpi:close-monthly {--year= : Year} {--month= : Month}';
    protected $description = 'Calculate and close monthly KPI scores for all employees';

    protected KpiMonthlyService $monthlyService;

    public function __construct(KpiMonthlyService $monthlyService)
    {
        parent::__construct();
        $this->monthlyService = $monthlyService;
    }

    public function handle(): int
    {
        $year = $this->option('year') ?: now()->subMonth()->year;
        $month = $this->option('month') ?: now()->subMonth()->month;

        $this->info("Closing monthly KPI for {$year}-{$month}");

        $employees = Employee::active()->get();
        $bar = $this->output->createProgressBar($employees->count());
        $bar->start();

        $success = 0;
        $errors = 0;

        foreach ($employees as $employee) {
            try {
                $result = $this->monthlyService->calculateMonthlyScore($employee->id, $year, $month);
                if ($result['status'] === 'success') {
                    $success++;
                } else {
                    $errors++;
                    $this->warn("\nFailed for employee {$employee->id}: {$result['message']}");
                }
            } catch (\Exception $e) {
                $errors++;
                $this->warn("\nError for employee {$employee->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Completed: {$success} employees");
        if ($errors > 0) {
            $this->warn("⚠ Errors: {$errors} employees");
        }

        return Command::SUCCESS;
    }
}
