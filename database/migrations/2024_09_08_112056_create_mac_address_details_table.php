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
        Schema::create('mac_address_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mac_address_id')->constrained('mac_addresses')->cascadeOnDelete();
            $table->string('phone_number');
            $table->string('ip_address')->nullable();
            $table->string('browser_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mac_address_details');
    }
};
