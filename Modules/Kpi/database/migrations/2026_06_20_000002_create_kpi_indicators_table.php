<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('key');                  // 'present', 'late', 'task', 'behavior', 'bonus', 'penalty'
            $table->string('name');                 // English name
            $table->string('name_bn')->nullable();   // Bengali name (optional)
            $table->decimal('weight_percentage', 5, 2);
            $table->decimal('point_per_unit', 8, 2)->nullable();  // Present=+1, Late=-2
            $table->decimal('default_max_score', 8, 2)->nullable(); // Behavior/Bonus/Penalty = 10
            $table->enum('count_behavior', ['Always Count', 'Optional Count']);
            // 'Always Count' = Present, Late, Task (target auto creates)
            // 'Optional Count' = Behavior, Bonus, Penalty (only if manager inputs)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('kpi_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_indicators');
    }
};
