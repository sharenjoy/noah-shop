<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sharenjoy\NoahCms\Models\Giftproduct;
use Spatie\Translatable\HasTranslations;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (in_array(HasTranslations::class, class_uses(Giftproduct::class))) {
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

        Schema::create('giftproducts', function (Blueprint $table) use ($fieldDataType) {
            $table->id();
            $table->foreignId('product_specification_id')->nullable()->constrained()->onDelete('cascade');
            $table->{$fieldDataType['title']}('title');
            $table->{$fieldDataType['description']}('description')->nullable();
            $table->{$fieldDataType['content']}('content')->nullable();
            $table->integer('img')->nullable();
            $table->text('album')->nullable();
            $table->string('slug', 100);
            $table->unsignedInteger('order_column')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giftproducts');
    }
};
