<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Shipped;

use Sharenjoy\NoahShop\Resources\Shop\ShippedOrderResource;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\BaseViewOrderLists;

class ViewOrderPickingList extends BaseViewOrderLists
{
    protected static string $resource = ShippedOrderResource::class;

    protected static string $view = 'noah-shop::pages.picking-list';

    public function getTitle(): string
    {
        return __('noah-cms::noah-cms.view_order_picking_list');
    }
}
