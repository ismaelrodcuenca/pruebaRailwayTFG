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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->decimal('base', 7, 2);
            $table->decimal('taxes',7,2);
            $table->decimal('total',7,2);
            $table->boolean('is_refund')->default(0);
            $table->boolean('is_down_payment')->default(0);
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders');
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('store_id')->constrained('stores');
            $table->foreignId('company_id')->nullable()->constrained('companies');
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->foreignId('user_id')->constrained('users');
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
