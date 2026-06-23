<?php

namespace Modules\Employee\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Employee\Models\Employee;
use Modules\Employee\Notifications\BirthdayNotification;

class CheckUpcomingBirthdays extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'employee:check-birthdays';

    /**
     * The console command description.
     */
    protected $description = 'Send birthday notifications for employees whose birthdays are within the next 7 days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking upcoming birthdays...');

        $today = Carbon::today();

        $employees = Employee::query()
            ->whereHas('personalInfo', function ($query) {
                $query->whereNotNull('date_of_birth');
            })
            ->with(['personalInfo', 'user'])
            ->active()
            ->get();

        $count = 0;

        foreach ($employees as $employee) {

            if (!$employee->personalInfo) {
                $this->warn("Employee {$employee->id} has no personal info.");
                continue;
            }

            if (!$employee->user) {
                $this->warn("Employee {$employee->id} has no user account.");
                continue;
            }

            $dob = Carbon::parse($employee->personalInfo->date_of_birth);

            // Birthday for current year
            $birthday = Carbon::create(
                $today->year,
                $dob->month,
                $dob->day
            )->startOfDay();

            // If birthday already passed this year
            if ($birthday->lt($today)) {
                $birthday->addYear();
            }

            $daysUntil = $today->diffInDays($birthday);

            $this->line(
                "{$employee->full_name} | DOB: {$dob->format('Y-m-d')} | Birthday: {$birthday->format('Y-m-d')} | Days Remaining: {$daysUntil}"
            );

            // Notify if birthday is within next 7 days
            if ($daysUntil >= 1 && $daysUntil <= 7) {

                $employee->user->notify(
                    new BirthdayNotification(
                        $employee,
                        $daysUntil
                    )
                );

                $count++;

                $this->info(
                    "✓ Notification sent to {$employee->full_name} ({$daysUntil} day(s) remaining)"
                );
            }
        }

        $this->newLine();
        $this->info("Done. {$count} notification(s) sent.");

        return self::SUCCESS;
    }
}