<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('break_minutes')->default(0);
            $table->integer('grace_in_min')->default(0);
            $table->integer('grace_out_min')->default(0);
            $table->float('work_hours')->nullable();
            $table->boolean('is_night_shift')->default(false);
            $table->boolean('is_flexible')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};