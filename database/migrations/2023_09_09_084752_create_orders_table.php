<?php

use App\Enums\PaidStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_gateway_id')->nullable()->constrained('payment_gateways');
            $table->foreignId('delivery_gateway_id')->nullable()->constrained('delivery_gateways');
            $table->foreignId('current_status_id')->nullable()->constrained('statuses');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons');
            $table->decimal('coupon_value', 20, 2)->default(0);
            $table->decimal('delivery_charge', 20, 2)->default(0);
            $table->decimal('buy_price', 20, 2)->default(0);
            $table->decimal('mrp', 20, 2)->default(0);
            $table->decimal('sell_price', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('special_discount', 20, 2)->default(0);
            $table->decimal('additional_cost', 8, 2)->default(0);
            $table->decimal('raw_material_cost', 8, 2)->default(0);
            $table->decimal('net_order_price', 20, 2)->default(0);
            $table->decimal('advance_payment', 20, 2)->default(0);
            $table->decimal('payable_price', 20, 2)->default(0);
            $table->decimal('due', 20, 2)->default(0);
            $table->decimal('courier_payable', 20, 2)->default(0);
            $table->string('paid_status')->default(PaidStatusEnum::UNPAID->value);
            $table->string('phone_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('district')->nullable();
            $table->text('address_details')->nullable()->comment('Shipping address');
            $table->string('order_from')->nullable();
            $table->integer('consignment_id')->nullable();
            $table->string('tracking_code', 1024)->nullable();
            $table->string('courier_name')->nullable();
            $table->string('mac_address')->nullable();
            $table->foreignId('locked_by_id')->nullable()->constrained('users');
            $table->string('note', 1024)->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users');
            $table->timestamp('prepared_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
