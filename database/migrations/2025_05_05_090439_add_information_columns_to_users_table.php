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
        Schema::table('users', function (Blueprint $table) {
            $table->after('email', function (Blueprint $table) {
                $table->string('sn')->unique();
                $table->string('calling_code')->nullable();
                $table->string('mobile')->nullable();
                $table->string('birthday')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sn');
            $table->dropColumn('calling_code');
            $table->dropColumn('mobile');
            $table->dropColumn('birthday');
        });
    }
};
