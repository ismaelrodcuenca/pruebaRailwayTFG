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
        Schema::create('item_work_order', function (Blueprint $table) {
            $table->id();
            $table->double('modified_amount')->nullable();
            $table->foreignId('work_order_id')->constrained('work_orders');
            $table->foreignId('item_id')->constrained('items');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_work_order');
    }
};
