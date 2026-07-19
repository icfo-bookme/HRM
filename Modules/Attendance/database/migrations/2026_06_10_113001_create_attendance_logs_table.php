<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('device_id')->nullable();

            $table->dateTime('punch_datetime');

            $table->enum('punch_type', [
                'IN',
                'OUT',
                'BREAK_IN',
                'BREAK_OUT'
            ]);

            $table->enum('source', [
                'Device',
                'Mobile App',
                'Web',
                'Manual',
                'CSV',
                'API'
            ])->default('Device');

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('ip_address', 45)->nullable();

            $table->enum('verification_method', [
                'Fingerprint',
                'Face',
                'Card',
                'PIN',
                'GPS',
                'Manual'
            ])->nullable();

            $table->string('raw_log_id')->nullable();

            $table->boolean('is_processed')->default(false);
            $table->dateTime('processing_date')->nullable();

            $table->text('remarks')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('device_id')->references('id')->on('attendance_devices');

            $table->index(['employee_id', 'punch_datetime']);
            $table->index(['device_id', 'punch_datetime']);
            $table->index(['is_processed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};