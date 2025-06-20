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
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('status', 50)->index();
            $table->string('sn')->nullable();
            $table->string('provider')->nullable();
            $table->string('delivery_type')->index()->nullable();
            $table->string('name')->nullable();
            $table->string('calling_code')->nullable();
            $table->string('mobile')->nullable();
            $table->string('country')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('address')->nullable();
            $table->string('pickup_store_no')->nullable();
            $table->string('pickup_store_name')->nullable();
            $table->string('pickup_store_address')->nullable();
            $table->string('pickup_retail_name')->nullable();
            $table->string('postoffice_delivery_code')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipments');
    }
};
