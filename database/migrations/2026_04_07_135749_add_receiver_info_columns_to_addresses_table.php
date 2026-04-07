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
        Schema::table('addresses', function (Blueprint $table) {
            $table->after('user_id', function (Blueprint $table) {
                $table->string('name')->nullable();
                $table->string('calling_code')->nullable();
                $table->string('mobile')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('calling_code');
            $table->dropColumn('mobile');
        });
    }
};
