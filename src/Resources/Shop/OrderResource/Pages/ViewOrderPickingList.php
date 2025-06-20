<?php

namespace Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages;

use Sharenjoy\NoahShop\Resources\Shop\OrderResource;

class ViewOrderPickingList extends BaseViewOrderLists
{
    protected static string $resource = OrderResource::class;

    protected static string $view = 'noah-shop::pages.picking-list';

    public function getTitle(): string
    {
        return __('noah-cms::noah-cms.view_order_picking_list');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
