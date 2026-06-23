<?php

namespace Modules\Shift\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Shift\Models\Shift;

class ShiftDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Shift::updateOrCreate(
            ['name' => 'Morning Shift'],
            [
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'break_minutes' => 60,
                'grace_in_min' => 15,
                'grace_out_min' => 15,
                'work_hours' => 8,
                'is_night_shift' => false,
                'is_flexible' => false,
                'is_active' => true,
            ]
        );

        $this->command->info('✓ Shift seeded successfully!');
    }
}