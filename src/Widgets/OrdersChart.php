<?php

namespace Sharenjoy\NoahShop\Widgets;

use Filament\Widgets\ChartWidget;
use Sharenjoy\NoahCms\Actions\Shop\ShopFeatured;
use Sharenjoy\NoahShop\Resources\Traits\CanViewShop;

class OrdersChart extends ChartWidget
{
    use CanViewShop;

    protected static ?string $heading = 'Orders per month';

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return ShopFeatured::run('shop');
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [2433, 3454, 4566, 3300, 5545, 5765, 6787, 8767, 7565, 8576, 9686, 8996],
                    'fill' => 'start',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
