<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->enum('address_type', ['present', 'permanent', 'mailing', 'emergency']);
            $table->string('house_no', 100)->nullable();
            $table->string('road_no', 100)->nullable();
            $table->string('road_name', 200)->nullable();
            $table->string('village', 200)->nullable();
            $table->string('area', 200)->nullable();
            $table->string('post_office', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('upazila', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('division', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('Bangladesh');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index('employee_id');
            $table->index('address_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_addresses');
    }
};