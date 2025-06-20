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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_specification_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_shipment_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('preorder')->default(false);
            $table->string('type', 50)->nullable()->index();
            $table->unsignedTinyInteger('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('currency', 3);
            $table->text('content')->nullable();
            $table->json('product_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
