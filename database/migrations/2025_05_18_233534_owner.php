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
        Schema::create('owner', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('corportate_name');
            $table->string('CIF', 20);
            $table->string('address');
            $table->string('postal_code', 10);
            $table->string('city');
            $table->string('province');
            $table->string('country');
            $table->string('phone', 30);
            $table->string('corporate_email');
            $table->string('website')->nullable();
            $table->integer('foundation_year')->nullable();
            $table->string('sector')->nullable();
            $table->string('short_description', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner');
    }
};
