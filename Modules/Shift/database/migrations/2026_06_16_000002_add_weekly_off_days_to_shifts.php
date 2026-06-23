<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            // Stores array of weekday numbers: 0=Sunday, 1=Monday, ..., 6=Saturday
            $table->json('weekly_off_days')->nullable()->after('metadata');
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('weekly_off_days');
        });
    }
};