<?php

namespace Sharenjoy\NoahCms\Database\Factories\Survey;

use Illuminate\Database\Eloquent\Factories\Factory;
use Sharenjoy\NoahCms\Models\Survey\Survey;

class SurveyFactory extends Factory
{
    protected $model = Survey::class;

    public function definition()
    {
        return [
            'name' => [
                'en' => $this->faker->words(2, true),
                'zh_TW' => $this->faker->words(2, true),
            ],
        ];
    }
}
