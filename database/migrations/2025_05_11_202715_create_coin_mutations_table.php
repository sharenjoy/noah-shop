<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coin_mutations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('promo_id')->nullable()->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->morphs('coinable');
            $table->string('type')->index();
            $table->integer('amount');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coin_mutations');
    }
};
