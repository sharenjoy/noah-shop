<?php

namespace Sharenjoy\NoahCms\Database\Factories\Survey;

use Illuminate\Database\Eloquent\Factories\Factory;
use Sharenjoy\NoahCms\Models\Survey\Answer;
use Sharenjoy\NoahCms\Models\Survey\Question;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition()
    {
        return [
            'value' => [
                'en' => $this->faker->words(3, true),
                'zh_TW' => $this->faker->words(3, true),
            ],
            'question_id' => Question::factory(),
        ];
    }
}
