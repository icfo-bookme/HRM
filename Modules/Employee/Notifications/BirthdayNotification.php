<?php

namespace Modules\Employee\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Employee\Models\Employee;

class BirthdayNotification extends Notification
{
    use Queueable;

    public Employee $employee;
    public int $daysUntil;

    /**
     * Create a new notification instance.
     */
    public function __construct(Employee $employee, int $daysUntil)
    {
        $this->employee = $employee;
        $this->daysUntil = $daysUntil;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $name = $this->employee->full_name ?: 'Unknown Employee';
        $dob = $this->employee->personalInfo?->date_of_birth;

        return [
            'type'       => 'birthday',
            'employee_id' => $this->employee->id,
            'employee_name' => $name,
            'date_of_birth' => $dob?->format('Y-m-d'),
            'days_until'    => $this->daysUntil,
            'message'       => "{$name}'s birthday is in {$this->daysUntil} day(s).",
            'title'         => 'Upcoming Birthday 🎂',
        ];
    }
}