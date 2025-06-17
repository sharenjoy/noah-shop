<?php

// config for Sharenjoy/NoahShop
return [

    'plugins' => [
        'resources' => [
            \Sharenjoy\NoahShop\Resources\Survey\AnswerResource::class,
            \Sharenjoy\NoahShop\Resources\Survey\EntryResource::class,
            \Sharenjoy\NoahShop\Resources\Survey\SurveyResource::class,
        ],
        'pages' => [],
        'widgets' => [],
    ],

    'survey' => [
        'question' => [
            'types' => [
                'text' => 'Text',
                'number' => 'Number',
                'radio' => 'Radio',
                'multiselect' => 'Multiselect',
                'file' => 'File',
                'price' => 'Price',
            ],
        ],
    ],


];
