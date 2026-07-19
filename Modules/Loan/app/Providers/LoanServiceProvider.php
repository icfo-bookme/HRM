<?php

namespace Modules\Loan\Providers;

use Illuminate\Support\Facades\Gate;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class LoanServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Loan';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'loan';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    protected array $commands = [
        \Modules\Loan\Console\Commands\CheckOverdueInstallments::class,
    ];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Bootstrap any module services.
     */
    public function boot(): void
    {
        parent::boot();

        // Define Gates for loan management actions
        Gate::define('manage-loans', function ($user) {
            $employee = $user->employee;
            if (!$employee || !$employee->relationLoaded('designation')) {
                $employee?->load('designation');
            }
            if (!$employee || !$employee->designation) {
                return false;
            }

            // Allow management/HR/admin/finance roles to manage loans
            $managementTitles = [
                'HR Manager', 'HR Executive', 'Admin Manager',
                'Chief Financial Officer', 'Chief Technology Officer',
                'Operations Manager', 'Project Manager', 'Accountant',
                'Sales Manager', 'Marketing Manager',
            ];

            return in_array($employee->designation->title, $managementTitles);
        });
    }

    /**
     * Define module schedules.
     * 
     * @param $schedule
     */
    protected function configureSchedules(Schedule $schedule): void
    {
        $schedule->command('loan:check-overdue')->daily();
    }
}
