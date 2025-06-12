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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('work_order_number')->default(1);
            $table->integer('work_order_number_warranty')->nullable();
            $table->text('failure');
            $table->text('private_comment')->nullable();
            $table->text('comment')->nullable();
            $table->text('physical_condition');
            $table->text('humidity');
            $table->text('test');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('device_id')->constrained('devices');
            $table->foreignId('repair_time_id')->constrained('repair_times');
            $table->foreignId('store_id')->constrained('stores');
            $table->timestamps();
        });
        DB::unprepared('
            CREATE TRIGGER increment_work_order_number AFTER INSERT ON work_orders
            FOR EACH ROW
            BEGIN
                UPDATE stores
                SET work_order_number = work_order_number + 1
                WHERE id = NEW.store_id;
            END;
        ');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
