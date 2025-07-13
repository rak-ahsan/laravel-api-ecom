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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->onUpdate('cascade');
            $table->foreignId('product_id')->constrained('products')->onUpdate('cascade');
            $table->foreignId('attribute_value_id_1')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_2')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_3')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->decimal('buy_price', 8, 2)->default(0);
            $table->integer('quantity');
            $table->decimal('total', 16, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
