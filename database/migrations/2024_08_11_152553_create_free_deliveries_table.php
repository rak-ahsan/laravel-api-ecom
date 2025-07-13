<?php

use App\Enums\StatusEnum;
use App\Enums\FreeDeliveryTypeEnum;
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
        Schema::create('free_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default(FreeDeliveryTypeEnum::QUANTITY->value);
            $table->integer('quantity')->nullable();
            $table->decimal('price', 20, 2)->default(0);
            $table->string('status')->default(StatusEnum::ACTIVE->value);
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
        Schema::dropIfExists('free_deliveries');
    }
};
