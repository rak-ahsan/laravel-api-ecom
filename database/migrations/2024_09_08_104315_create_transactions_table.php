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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->decimal('payable_amount', 20, 2)->default(0);
            $table->string('type')->nullable();
            $table->foreignId('payment_gateway_id')->nullable()->constrained('payment_gateways');
            $table->string('payment_id')->nullable()->comment('This data will come from the gateway api.');
            $table->string('payment_gateway_trx_id')->nullable()->comment('This data will come from the gateway api.');
            $table->string('remark', 1000)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
