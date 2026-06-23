<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('attendance_devices', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('branch_id');

            $table->string('device_name', 150);
            $table->string('device_code', 50)->unique();

            $table->enum('device_type', [
                'Fingerprint',
                'Face',
                'Card',
                'Mobile App',
                'Web',
                'Manual'
            ]);

            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();

            $table->string('serial_number', 100)->unique();

            $table->string('ip_address', 45)->nullable();
            $table->string('port', 10)->nullable();

            $table->enum('communication_type', [
                'LAN',
                'WAN',
                'WiFi',
                'Cloud API',
                'USB'
            ])->default('LAN');

            $table->string('firmware_version', 50)->nullable();

            $table->string('timezone')->default('Asia/Dhaka');

            $table->string('location')->nullable();

            $table->dateTime('last_sync_at')->nullable();

            $table->enum('sync_status', [
                'Online',
                'Offline',
                'Syncing',
                'Error'
            ])->default('Offline');

            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->json('metadata')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->foreign('branch_id')->references('id')->on('branches');

            $table->index(['branch_id']);
            $table->index(['device_type']);
            $table->index(['sync_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_devices');
    }
};