<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sharenjoy\NoahCms\Models\UserLevel;
use Spatie\Translatable\HasTranslations;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (in_array(HasTranslations::class, class_uses(UserLevel::class))) {
            $fieldDataType = [
                'title' => 'json',
                'description' => 'json',
                'content' => 'json',
            ];
        } else {
            $fieldDataType = [
                'title' => 'string',
                'description' => 'text',
                'content' => 'text',
            ];
        }

        Schema::create('user_levels', function (Blueprint $table) use ($fieldDataType) {
            $table->id();
            $table->{$fieldDataType['title']}('title');
            $table->{$fieldDataType['description']}('description')->nullable();
            $table->{$fieldDataType['content']}('content')->nullable();
            $table->integer('img')->nullable();
            $table->text('album')->nullable();
            $table->unsignedTinyInteger('discount_percent')->nullable();
            $table->string('point_times', 10)->nullable();
            $table->unsignedInteger('level_up_amount')->nullable();
            $table->boolean('auto_level_up')->default(false);
            $table->boolean('forever')->default(false);
            $table->unsignedTinyInteger('level_duration')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_levels');
    }
};
