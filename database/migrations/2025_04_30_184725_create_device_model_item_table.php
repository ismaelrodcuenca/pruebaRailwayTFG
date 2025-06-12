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
        Schema::create('device_model_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_model_id')->constrained('device_models');
            $table->foreignId('item_id')->constrained('items');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_model_item');
    }
};
