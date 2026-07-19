<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 300);
            $table->date('holiday_date');
            $table->date('end_date')->nullable();
            $table->integer('total_days')->storedAs('DATEDIFF(COALESCE(end_date, holiday_date), holiday_date) + 1');
            $table->enum('holiday_type', ['Public','Government','Company','Optional','Religious','Festival'])->default('Public');
            $table->enum('applicable_to', ['All','Specific','Branch','Department'])->default('All');
            $table->boolean('is_recurring')->default(false);
            $table->boolean('yearly_recurring')->default(false);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
            $table->index('holiday_date', 'idx_holiday_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};