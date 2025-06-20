<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Shipped;

use Sharenjoy\NoahShop\Resources\Shop\ShippedOrderResource;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\BaseViewOrderLists;

class ViewOrderInfoList extends BaseViewOrderLists
{
    protected static string $resource = ShippedOrderResource::class;

    protected static string $view = 'noah-shop::pages.order-info-list';

    public function getTitle(): string
    {
        return __('noah-cms::noah-cms.view_order_info_list');
    }
}
