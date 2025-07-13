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
        Schema::create('campaign_product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_product_id')->nullable()->constrained('campaign_products')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_1')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_2')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->foreignId('attribute_value_id_3')->nullable()->constrained('attribute_values')->cascadeOnDelete();
            $table->boolean('is_default')->default(false);
            $table->decimal('buy_price', 20, 2)->default(0);
            $table->decimal('mrp', 20, 2)->default(0);
            $table->decimal('offer_price', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('offer_percent', 20, 2)->default(0);
            $table->string('img_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_product_variations');
    }
};
