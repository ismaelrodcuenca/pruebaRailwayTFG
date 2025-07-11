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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('document');
            $table->string('name');
            $table->string('surname');
            $table->string('surname2')->nullable();
            $table->string('phone_number');
            $table->string('phone_number_2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('document_type_id')->constrained('document_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
