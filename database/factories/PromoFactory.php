<?php

namespace Sharenjoy\NoahShop\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Sharenjoy\NoahShop\Enums\PromoAutoGenerateType;
use Sharenjoy\NoahShop\Enums\PromoDiscountType;
use Sharenjoy\NoahShop\Enums\PromoType;
use Sharenjoy\NoahShop\Models\Promo;
use Sharenjoy\NoahShop\Models\User;
use Spatie\Translatable\HasTranslations;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Sharenjoy\NoahShop\Models>
 */
class PromoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Promo::class;

    public function definition(): array
    {
        $coupon = $this->getCoupon();

        return [
            'type' => Arr::random(PromoType::cases()),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'content' => $this->getContent(),
            'slug' => fake()->unique()->word(10),
            'img' => \Spatie\MediaLibrary\MediaCollections\Models\Media::inRandomOrder()->first()->id,
            'is_active' => fake()->boolean(70),
            'published_at' => now(),

            'combined' => fake()->boolean(20), // 約 20% 為 true
            'forever' => fake()->boolean(10),

            'discount_type' => Arr::random(PromoDiscountType::cases()),
            'discount_amount' => fake()->randomFloat(0, 100, 1000), // 隨機折扣金額
            'discount_percent' => Arr::random([5, 10, 15, 20, 25, 30]), // 5%～30%

            'entire_order_discount_percent' => fake()->boolean(),
            'min_order_amount' => fake()->randomFloat(0, 2000, 5000),

            'min_quantity' => fake()->numberBetween(1, 4),
            'min_spend' => fake()->randomFloat(0, 500, 2000),

            'code' => $coupon['code'] ?? null,

            'usage_limit' => $coupon['usage_limit'] ?? null,
            'per_user_limit' => $coupon['per_user_limit'] ?? null,

            'auto_generate_type' => $coupon['auto_generate_type'] ?? null,
            'auto_generate_date' => $coupon['auto_generate_date'] ?? null,
            'auto_generate_day' => $coupon['auto_generate_day'] ?? null,

            'is_active' => true,
            'order_column' => fake()->optional()->numberBetween(1, 100),
            'started_at' => now(),
            'expired_at' => now()->addDays(fake()->numberBetween(7, 60)),
            'display_expired_at' => now()->addDays(fake()->numberBetween(7, 90)),

        ];
    }

    public function getCoupon()
    {
        $faker = [];
        $generateType = Arr::random(PromoAutoGenerateType::cases());

        $faker['code'] = strtoupper(fake()->unique()->bothify('####??'));

        $faker['usage_limit'] = fake()->numberBetween(1, 100);
        $faker['per_user_limit'] = fake()->numberBetween(1, 5);

        $faker['auto_generate_type'] = $generateType;

        if ($generateType == 'yearly') {
            $faker['auto_generate_date'] = fake()->dateBetween('now', '+1 year');
        } elseif ($generateType == 'monthly') {
            $faker['auto_generate_day'] = fake()->numberBetween(1, 28); // 為避免 2 月問題
        } elseif ($generateType == 'everyday') {
            //
        }

        return $faker;
    }

    protected function getTitle(): array|string
    {
        if (in_array(HasTranslations::class, class_uses(Promo::class))) {
            return [
                'en' => fake('en')->sentence(),
                'zh_TW' => fake()->sentence(),
            ];
        }

        return fake()->sentence();
    }

    protected function getDescription(): array|string
    {
        if (in_array(HasTranslations::class, class_uses(Promo::class))) {
            return [
                'en' => fake('en')->paragraph(),
                'zh_TW' => fake()->paragraph(),
            ];
        }

        return fake()->paragraph();
    }

    protected function getContent(): array|string
    {
        if (in_array(HasTranslations::class, class_uses(Promo::class))) {
            return [
                'en' => fake('en')->text(),
                'zh_TW' => fake()->text(),
            ];
        }

        return fake()->text();
    }
}
