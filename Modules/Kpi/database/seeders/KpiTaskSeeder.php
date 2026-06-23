<?php

namespace Modules\Kpi\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Employee;

class KpiTaskSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::active()->get();
        
        if ($employees->isEmpty()) {
            return;
        }

        $priorities = ['Low', 'Medium', 'High', 'Critical'];
        $statuses = ['Pending', 'In Progress', 'Completed', 'Completed', 'Completed']; // Weighted towards completed

        $tasks = [];
        $now = now();

        // Create 3-5 tasks per employee
        foreach ($employees as $employee) {
            $taskCount = rand(3, 5);
            
            for ($i = 0; $i < $taskCount; $i++) {
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];
                $targetScore = rand(50, 200);
                
                $assignedDate = $now->copy()->subDays(rand(1, 30));
                $deadline = $assignedDate->copy()->addDays(rand(7, 30));

                $task = [
                    'employee_id' => $employee->id,
                    'assigned_by' => 1, // Assuming admin user ID is 1
                    'title' => $this->getRandomTaskTitle(),
                    'description' => $this->getRandomDescription(),
                    'target_score' => $targetScore,
                    'obtained_score' => $status === 'Completed' ? rand(40, $targetScore) : null,
                    'priority' => $priority,
                    'assigned_date' => $assignedDate,
                    'deadline' => $deadline,
                    'status' => $status,
                    'completed_at' => $status === 'Completed' ? $assignedDate->copy()->addDays(rand(1, 10)) : null,
                    'completion_note' => $status === 'Completed' ? 'Task completed successfully' : null,
                    'created_at' => $assignedDate,
                    'updated_at' => $status === 'Completed' ? $assignedDate->copy()->addDays(rand(1, 10)) : $assignedDate,
                ];

                $tasks[] = $task;
            }
        }

        DB::table('kpi_tasks')->insert($tasks);
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
            null, // Some tasks may not have descriptions
            null,
        ];

        return $descriptions[array_rand($descriptions)];
    }
}