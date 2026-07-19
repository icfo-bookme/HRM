<?php

namespace Modules\Kpi\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;

class KpiTaskSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::active()->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Skipping KPI task seeder.');
            return;
        }

        $adminEmployee = Employee::active()->first();
        $assignedById = $adminEmployee ? $adminEmployee->id : 1;

        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        $statuses = ['Pending', 'In Progress', 'Completed', 'Completed', 'Completed'];

        $tasks = [];
        $now = now();

        foreach ($employees as $employee) {
            $taskCount = rand(3, 5);

            for ($i = 0; $i < $taskCount; $i++) {
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];
                $targetScore = rand(50, 200);

                $assignedDate = $now->copy()->subDays(rand(1, 30));
                $deadline = $assignedDate->copy()->addDays(rand(7, 30));

                $tasks[] = [
                    'employee_id' => $employee->id,
                    'assigned_by' => $assignedById,
                    'title' => $this->getRandomTaskTitle(),
                    'description' => $this->getRandomDescription(),
                    'target_score' => $targetScore,
                    'obtained_score' => $status === 'Completed' ? rand(40, $targetScore) : null,
                    'priority' => $priority,
                    'assigned_date' => $assignedDate->toDateString(),
                    'deadline' => $deadline->toDateString(),
                    'status' => $status,
                    'completed_at' => $status === 'Completed' ? $assignedDate->copy()->addDays(rand(1, 10)) : null,
                    'completion_note' => $status === 'Completed' ? 'Task completed successfully' : null,
                    'created_at' => $assignedDate,
                    'updated_at' => $status === 'Completed' ? $assignedDate->copy()->addDays(rand(1, 10)) : $assignedDate,
                ];
            }
        }

        foreach ($tasks as $task) {
            DB::table('kpi_tasks')->updateOrInsert(
                ['employee_id' => $task['employee_id'], 'title' => $task['title'], 'assigned_date' => $task['assigned_date']],
                $task
            );
        }

        $this->command->info('✓ KPI tasks seeded successfully!');
    }

    private function getRandomTaskTitle(): string
    {
        $titles = [
            'Complete monthly sales report',
            'Update customer database',
            'Prepare presentation for client meeting',
            'Review and approve leave applications',
            'Conduct team performance review',
            'Update project documentation',
            'Train new team members',
            'Prepare budget proposal',
            'Conduct market research',
            'Develop marketing strategy',
            'Implement new software feature',
            'Write technical documentation',
            'Organize team building event',
            'Prepare quarterly financial report',
            'Update company website content',
            'Conduct employee onboarding',
            'Review inventory levels',
            'Prepare training materials',
            'Analyze customer feedback',
            'Develop process improvements',
        ];

        return $titles[array_rand($titles)];
    }

    private function getRandomDescription(): ?string
    {
        $descriptions = [
            'Complete this task within the specified deadline and ensure quality standards are met.',
            'Work with the team to accomplish this task efficiently.',
            'Focus on accuracy and attention to detail while completing this assignment.',
            'Coordinate with relevant departments to ensure smooth execution.',
            'Document the process and outcomes for future reference.',
            null,
            null,
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
