<?php

namespace Modules\Company\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Company\Models\Company;

class CompanyDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Company::updateOrCreate(
            ['name' => 'HRM Professional Ltd.'],
            [
                'legal_name' => 'HRM Professional Limited',
                'trade_license' => 'TR-2020-00123',
                'bin_number' => 'BIN-123456789',
                'tin_number' => 'TIN-987654321',
                'industry' => 'Software & HR Services',
                'founded_year' => 2020,
                'address' => '123 Gulshan Avenue, Gulshan-1',
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
                'phone' => '+880-2-9876543',
                'email' => 'info@hrmprofessional.com',
                'website' => 'https://hrmprofessional.com',
                'timezone' => 'Asia/Dhaka',
                'date_format' => 'Y-m-d',
                'fiscal_year_start' => '2024-07-01',
                'is_active' => 1,
            ]
        );

        $this->command->info('✓ Company seeded successfully!');
    }
}