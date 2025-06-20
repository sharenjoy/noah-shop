<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sharenjoy\NoahCms\Models\Product;
use Spatie\Translatable\HasTranslations;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (in_array(HasTranslations::class, class_uses(Product::class))) {
            $fieldDataType = [
                'title' => 'json',
                'description' => 'json',
                'content' => 'json',
                'specs' => 'json',
            ];
        } else {
            $fieldDataType = [
                'title' => 'string',
                'description' => 'text',
                'content' => 'text',
                'specs' => 'text',
            ];
        }

        Schema::create('products', function (Blueprint $table) use ($fieldDataType) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('slug', 50)->index();
            $table->{$fieldDataType['title']}('title');
            $table->{$fieldDataType['description']}('description')->nullable();
            $table->{$fieldDataType['content']}('content')->nullable();
            $table->{$fieldDataType['specs']}('specs')->nullable();
            $table->integer('img')->nullable();
            $table->text('album')->nullable();
            $table->json('stock_method')->nullable();
            $table->json('product_limit')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->boolean('is_single_spec')->default(false)->index();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamp('published_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
