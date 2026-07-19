<?php

namespace Modules\Kpi\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Kpi\Services\KpiDailyService;

class ProcessDailyKpi extends Command
{
    protected $signature = 'kpi:process-daily {--date= : The date to process (Y-m-d format)}';
    protected $description = 'Process daily KPI attendance data for all employees';

    protected KpiDailyService $dailyService;

    public function __construct(KpiDailyService $dailyService)
    {
        parent::__construct();
        $this->dailyService = $dailyService;
    }

    public function handle(): int
    {
        $dateStr = $this->option('date');
        $date = $dateStr ? Carbon::parse($dateStr) : Carbon::yesterday();

        $this->info("Processing daily KPI for date: {$date->format('Y-m-d')}");

        $result = $this->dailyService->processDailyAttendance($date);

        if ($result['status'] === 'success') {
            $this->info("✓ Processed: {$result['processed']} employees");
            if ($result['errors'] > 0) {
                $this->warn("⚠ Errors: {$result['errors']} employees");
            }
            return Command::SUCCESS;
        }

        $this->error("✗ Error: {$result['message']}");
        return Command::FAILURE;
    }
}
