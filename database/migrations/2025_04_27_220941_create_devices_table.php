<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->boolean('has_no_serial_or_imei')->default(false);
            $table->string('serial_number')->nullable();
            $table->string('IMEI')->nullable();
            $table->string('colour');
            $table->string('unlock_code')->nullable();
            $table->foreignId('device_model_id')->constrained('device_models');
            $table->foreignId('client_id')->constrained('clients');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
