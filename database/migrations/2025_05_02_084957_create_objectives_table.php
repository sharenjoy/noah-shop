<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sharenjoy\NoahCms\Enums\ObjectiveStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('objectives', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 50)->index();
            $table->string('status', 50)->default(ObjectiveStatus::New->value);
            $table->text('description')->nullable();
            $table->integer('count')->default(0);
            $table->json('user')->nullable();
            $table->json('product')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('objectiveables', function (Blueprint $table) {
            $table->foreignId('objective_id');
            $table->morphs('objectiveable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objectiveables');
        Schema::dropIfExists('objectives');
    }
};
