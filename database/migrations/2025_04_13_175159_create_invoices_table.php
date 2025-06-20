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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('type', 50)->index();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('currency', 3);
            $table->string('donate_code')->nullable();
            $table->string('holder_type', 50)->nullable()->index();
            $table->string('holder_code')->nullable();
            $table->string('company_title')->nullable();
            $table->string('company_code')->nullable();
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
