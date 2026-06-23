<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 300);
            $table->string('legal_name', 300)->nullable();
            $table->string('trade_license', 100)->nullable();
            $table->string('bin_number', 50)->nullable();
            $table->string('tin_number', 50)->nullable();
            $table->string('industry', 150)->nullable();
            $table->year('founded_year')->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('Bangladesh');
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('timezone', 50)->default('Asia/Dhaka');
            $table->string('date_format', 20)->default('Y-m-d');
            $table->date('fiscal_year_start')->nullable();
            $table->boolean('is_active')->default(1);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->index('is_active');
            $table->index('city');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};