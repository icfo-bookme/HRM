<?php

namespace Modules\Branch\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Branch\Models\Branch;

class BranchDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Branch::updateOrCreate(
            ['code' => 'DHK-001'],
            [
                'company_id' => 1,
                'name' => 'Head Office - Dhaka',
                'address' => '123 Gulshan Avenue, Gulshan-1',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'phone' => '+880-2-9876543',
                'email' => 'dhaka@hrmprofessional.com',
                'is_head_office' => true,
                'is_active' => true,
            ]
        );

        $this->command->info('✓ Branch seeded successfully!');
    }
}