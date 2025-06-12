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
        Schema::create('status_work_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('status_id')->constrained('statuses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_work_order');
    }
};
