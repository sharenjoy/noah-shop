<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Issued;

use Sharenjoy\NoahShop\Resources\Shop\IssuedOrderResource;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\BaseViewOrderLists;

class ViewOrderPickingList extends BaseViewOrderLists
{
    protected static string $resource = IssuedOrderResource::class;

    protected static string $view = 'noah-shop::pages.picking-list';

    public function getTitle(): string
    {
        return __('noah-cms::noah-cms.view_order_picking_list');
    }
}
