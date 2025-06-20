<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\New;

use Sharenjoy\NoahShop\Resources\Shop\NewOrderResource;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\BaseViewOrderLists;

class ViewOrderPickingList extends BaseViewOrderLists
{
    protected static string $resource = NewOrderResource::class;

    protected static string $view = 'noah-shop::pages.picking-list';

    public function getTitle(): string
    {
        return __('noah-cms::noah-cms.view_order_picking_list');
    }
}
