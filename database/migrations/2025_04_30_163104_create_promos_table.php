<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sharenjoy\NoahCms\Enums\PromoAutoGenerateType;
use Sharenjoy\NoahCms\Models\Promo;
use Spatie\Translatable\HasTranslations;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (in_array(HasTranslations::class, class_uses(Promo::class))) {
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

        Schema::create('promos', function (Blueprint $table) use ($fieldDataType) {
            $table->id();
            $table->string('type', 50)->index();
            $table->string('slug', 100);
            $table->{$fieldDataType['title']}('title');
            $table->{$fieldDataType['description']}('description')->nullable();
            $table->{$fieldDataType['content']}('content')->nullable();
            $table->integer('img')->nullable();
            $table->text('album')->nullable();

            $table->boolean('combined')->default(false);
            $table->boolean('forever')->default(false);

            $table->string('discount_type')->index();
            $table->decimal('discount_amount', 10, 2)->nullable(); // 折扣金額（例如 100 元）
            $table->unsignedTinyInteger('discount_percent')->nullable(); // 折扣百分比（例如 10%）
            $table->unsignedInteger('discount_percent_limit_amount')->nullable(); // 折扣上限金額（例如 100 元）

            $table->decimal('min_order_amount', 10, 2)->nullable(); // 最低訂單金額

            $table->integer('min_quantity')->nullable();      // 滿件件數
            $table->decimal('min_spend', 10, 2)->nullable();      // 滿額數

            $table->string('code')->nullable();           // 專屬折扣碼（例如 BDAY-2025-0001）

            $table->unsignedInteger('usage_limit')->nullable();      // 總使用次數限制
            $table->tinyInteger('per_user_limit')->nullable();   // 每人可使用次數

            $table->string('auto_generate_type')->default(PromoAutoGenerateType::Never->value);
            $table->string('auto_generate_date')->nullable(); // 每年的哪一天
            $table->string('auto_generate_day')->nullable(); // 每月的幾號
            $table->boolean('auto_assign_to_user')->default(false);

            $table->boolean('is_active')->default(false)->index();
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->datetime('expired_at')->nullable();
            $table->datetime('display_expired_at')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('promoables', function (Blueprint $table) {
            $table->foreignId('promo_id');
            $table->morphs('promoable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promoables');
        Schema::dropIfExists('promos');
    }
};
