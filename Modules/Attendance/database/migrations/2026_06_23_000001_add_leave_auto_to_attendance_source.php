<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->enum('source', [
                'Device',
                'Manual',
                'CSV',
                'API',
                'Mobile App',
                'Web',
                'Leave Auto'
            ])->default('Device')->change();
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->enum('source', [
                'Device',
                'Manual',
                'CSV',
                'API',
                'Mobile App',
                'Web'
            ])->default('Device')->change();
        });
    }
};