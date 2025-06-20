<?php

namespace Sharenjoy\NoahShop\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Sharenjoy\NoahShop\Models\Brand;
use Spatie\Translatable\HasTranslations;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Brand::class;

    public function definition(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'content' => $this->getContent(),
            'slug' => fake()->unique()->word(10),
            'img' => \Spatie\MediaLibrary\MediaCollections\Models\Media::inRandomOrder()->first()->id,
            'is_active' => fake()->boolean(70),
        ];
    }

    protected function getName(): array|string
    {
        if (in_array(HasTranslations::class, class_uses(Brand::class))) {
            return [
                'en' => fake('en')->word(),
                'zh_TW' => fake()->word(),
            ];
        }

        return fake()->word();
    }

    protected function getDescription(): array|string
    {
        if (in_array(HasTranslations::class, class_uses(Brand::class))) {
            return [
                'en' => fake('en')->paragraph(),
                'zh_TW' => fake()->paragraph(),
            ];
        }

        return fake()->paragraph();
    }

    protected function getContent(): array|string
    {
        if (in_array(HasTranslations::class, class_uses(Brand::class))) {
            return [
                'en' => fake('en')->text(),
                'zh_TW' => fake()->text(),
            ];
        }

        return fake()->text();
    }
}
