<?php

namespace Sharenjoy\NoahCms\Database\Factories\Survey;

use Illuminate\Database\Eloquent\Factories\Factory;
use Sharenjoy\NoahCms\Models\Survey\Question;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        return [
            'content' => [
                'en' => $this->faker->sentence(),
                'zh_TW' => $this->faker->sentence(),
            ],
        ];
    }
}
