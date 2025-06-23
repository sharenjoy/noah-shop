<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Shippable;

use Sharenjoy\NoahShop\Resources\Shop\ShippableOrderResource;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\BaseViewOrderLists;

class ViewOrderInfoList extends BaseViewOrderLists
{
    protected static string $resource = ShippableOrderResource::class;

    protected static string $view = 'noah-shop::pages.order-info-list';

    public function getTitle(): string
    {
        return __('noah-shop::noah-shop.view_order_info_list');
    }
}
