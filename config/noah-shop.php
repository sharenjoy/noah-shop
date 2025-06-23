<?php

// config for Sharenjoy/NoahShop
return [

    'models' => [
        //
    ],

    'plugins' => [
        'resources' => [
            \Sharenjoy\NoahShop\Resources\Survey\AnswerResource::class,
            \Sharenjoy\NoahShop\Resources\Survey\EntryResource::class,
            \Sharenjoy\NoahShop\Resources\Survey\SurveyResource::class,
            \Sharenjoy\NoahShop\Resources\UserResource::class,
            \Sharenjoy\NoahShop\Resources\ProductResource::class,
            \Sharenjoy\NoahShop\Resources\ProductSpecificationResource::class,
            \Sharenjoy\NoahShop\Resources\BrandResource::class,
            \Sharenjoy\NoahShop\Resources\CurrencyResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\OrderResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\NewOrderResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\ShippableOrderResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\ShippedOrderResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\DeliveredOrderResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\IssuedOrderResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\CouponPromoResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\MinSpendPromoResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\MinQuantityPromoResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\ObjectiveResource::class,
            \Sharenjoy\NoahShop\Resources\Shop\UserLevelResource::class,
            \Sharenjoy\NoahShop\Resources\GiftproductResource::class,
        ],
        'pages' => [],
        'widgets' => [],
    ],

    'shop-feature' => [
        'shop' => env('NOAHSHOP_FEATURE_SHOP', true),
        'coin-point' => env('NOAHSHOP_FEATURE_POINT', true),
        'coin-shoppingmoney' => env('NOAHSHOP_FEATURE_SHOPPINGMONEY', true),
    ],

    'promo' => [
        'conditions_decrypter' => env('PROMO_CONDITIONS_DECRYPTER', 'ronaldiscreator'),
        'conditions_divider' => env('PROMO_CONDITIONS_DIVIDER', ':::'),
        'coupon_divider' => env('PROMO_COUPON_DIVIDER', '::'),
    ],

    'donate_code' => [
        // 322833 => '天主教花蓮教區醫療財團法人',
        // 17930 => '社團法人台灣環境資訊協會',
        // 876 => '財團法人心路社會福利基金會',
        // 2880 => '台灣原生植物保育協會',
        // 7495 => '社團法人臺灣野灣野生動物保育協會',
    ],

    // 這些 enum 的值會被隱藏在選單中
    'hidden' => [
        'OrderStatus' => [
            'initial',
        ],
        'DeliveryProvider' => [
            'tcat',
            'fedex',
        ],
        'StockMethod' => [
            'preorderable',
        ],
        'CoinType' => [
            'shoppingmoney',
        ],
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
