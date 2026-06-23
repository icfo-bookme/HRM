<?php

namespace Modules\Kpi\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Nwidart\Modules\Support\ModuleServiceProvider;

class KpiServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'KPI';
    protected string $nameLower = 'kpi';

    protected array $commands = [
        \Modules\Kpi\Console\Commands\ProcessDailyKpi::class,
        \Modules\Kpi\Console\Commands\CloseMonthlyKpi::class,
    ];

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();
    }

    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command('kpi:process-daily')->daily();
        $schedule->command('kpi:close-monthly')->monthly();
    }
}
