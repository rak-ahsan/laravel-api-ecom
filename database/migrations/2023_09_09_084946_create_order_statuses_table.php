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
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->constrained('orders')->onUpdate('cascade');
            $table->foreignId('status_id')->nullable()->constrained('statuses')->onUpdate('cascade');
            $table->timestamps();

            $table->primary(['order_id', 'status_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
