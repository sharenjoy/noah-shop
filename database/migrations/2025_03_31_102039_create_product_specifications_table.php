<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sharenjoy\NoahCms\Models\ProductSpecification;
use Spatie\Translatable\HasTranslations;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (in_array(HasTranslations::class, class_uses(ProductSpecification::class))) {
            $fieldDataType = [
                'content' => 'json',
            ];
        } else {
            $fieldDataType = [
                'content' => 'text',
            ];
        }

        Schema::create('product_specifications', function (Blueprint $table) use ($fieldDataType) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->json('spec_detail_name');
            $table->string('no')->nullable()->index();
            $table->string('sku')->nullable()->index();
            $table->string('barcode')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('price')->nullable();
            $table->integer('compare_price')->nullable();
            $table->integer('cost')->nullable();
            $table->integer('img')->nullable();
            $table->text('album')->nullable();
            $table->{$fieldDataType['content']}('content')->nullable();
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
