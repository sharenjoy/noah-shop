<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Delivered;

use Sharenjoy\NoahShop\Resources\Shop\DeliveredOrderResource;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\BaseViewOrderLists;

class ViewOrderPickingList extends BaseViewOrderLists
{
    protected static string $resource = DeliveredOrderResource::class;

    protected static string $view = 'noah-shop::pages.picking-list';

    public function getTitle(): string
    {
        return __('noah-cms::noah-cms.view_order_picking_list');
    }
}
