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
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained()->onDelete('cascade'); // 所屬會員
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 所屬會員
            $table->string('status'); // 狀態（例如 new、used、expired）
            $table->string('code')->unique()->index();           // 專屬折扣碼（例如 BDAY-2025-0001）
            $table->timestamp('started_at')->nullable();
            $table->datetime('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_coupons');
    }
};
