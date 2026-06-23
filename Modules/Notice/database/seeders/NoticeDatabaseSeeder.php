<?php

namespace Modules\Notice\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Notice\Models\Notice;

class NoticeDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $notices = [
            [
                'title'       => 'Company Picnic 2026',
                'description' => 'We are excited to announce the annual company picnic! Join us for a day of fun, food, and games.',
                'notice_type' => 'Event',
                'priority'    => 'Medium',
                'publish_date'=> now(),
                'expiry_date' => now()->addDays(14),
                'target_type' => 'All',
                'is_active'   => true,
            ],
            [
                'title'       => 'Office Closed - National Holiday',
                'description' => 'The office will remain closed on the upcoming national holiday. Please plan accordingly.',
                'notice_type' => 'Holiday',
                'priority'    => 'High',
                'publish_date'=> now(),
                'expiry_date' => now()->addDays(7),
                'target_type' => 'All',
                'is_active'   => true,
                'is_popup'    => true,
            ],
            [
                'title'       => 'New HR Policy Update',
                'description' => 'Please review the updated HR policies. Key changes include remote work policy and leave encashment rules.',
                'notice_type' => 'HR',
                'priority'    => 'Urgent',
                'publish_date'=> now(),
                'expiry_date' => now()->addDays(30),
                'target_type' => 'All',
                'is_active'   => true,
                'is_pinned'   => true,
            ],
            [
                'title'       => 'Payroll Schedule Change',
                'description' => 'Effective next month, payroll processing will shift from the 5th to the 7th of every month.',
                'notice_type' => 'Payroll',
                'priority'    => 'High',
                'publish_date'=> now(),
                'expiry_date' => now()->addDays(60),
                'target_type' => 'Department',
                'is_active'   => true,
            ],
            [
                'title'       => 'Fire Drill Scheduled',
                'description' => 'A mandatory fire drill will be conducted this Friday at 3 PM. All employees must participate.',
                'notice_type' => 'General',
                'priority'    => 'Medium',
                'publish_date'=> now(),
                'expiry_date' => now()->addDays(5),
                'target_type' => 'All',
                'is_active'   => true,
            ],
        ];

        foreach ($notices as $notice) {
            Notice::create($notice);
        }

        $this->command->info('Notice seeded successfully.');
    }
}