<?php

namespace Sharenjoy\NoahShop\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Sharenjoy\NoahShop\Actions\ResolveProductSpecsDataToRecords;
use Sharenjoy\NoahShop\Models\Brand;
use Sharenjoy\NoahShop\Models\Product;
use Spatie\Translatable\HasTranslations;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Sharenjoy\NoahShop\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Product::class;

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            ResolveProductSpecsDataToRecords::run($product->specs, $product, 'create');
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => Brand::inRandomOrder()->first()->id,
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'content' => $this->getContent(),
            'specs' => fake()->randomElement([
                json_decode('[{"spec_name": "材質", "spec_details": [{"detail_name": "楓木柄"}, {"detail_name": "藤製柄"}]}, {"spec_name": "軟硬度", "spec_details": [{"detail_name": "極軟 110"}, {"detail_name": "軟 120"}, {"detail_name": "中軟 130"}]}]', true),
                json_decode('[{"spec_name": "顏色", "spec_details": [{"detail_name": "黑"}, {"detail_name": "白"}]}, {"spec_name": "大小", "spec_details": [{"detail_name": "S"}, {"detail_name": "M"}, {"detail_name": "L"}]}]', true),
            ]),
            'is_single_spec' => false,
            'slug' => fake()->unique()->word(10),
            'img' => \Spatie\MediaLibrary\MediaCollections\Models\Media::inRandomOrder()->first()->id,
            'is_active' => fake()->boolean(70),
            'published_at' => now(),
        ];
    }

    protected function getTitle(): array|string
    {
        if (in_array(HasTranslations::class, class_uses(Product::class))) {
            return [
                'en' => fake('en')->sentence(),
                'zh_TW' => fake()->sentence(),
            ];
        }

        return fake()->sentence();
    }

    protected function getDescription(): array|string
    {
        if (in_array(HasTranslations::class, class_uses(Product::class))) {
            return [
                'en' => fake('en')->paragraph(),
                'zh_TW' => fake()->paragraph(),
            ];
        }

        return fake()->paragraph();
    }

    protected function getContent(): array|string
    {
        if (in_array(HasTranslations::class, class_uses(Product::class))) {
            return [
                'en' => fake('en')->text(),
                'zh_TW' => fake()->text(),
            ];
        }

        return fake()->text();
    }
}
