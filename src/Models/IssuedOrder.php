<?php

namespace Sharenjoy\NoahShop\Models;

use Sharenjoy\NoahShop\Models\BaseOrder;
use Sharenjoy\NoahShop\Resources\Shop\OrderResource\Pages\Actions\ViewOrderItemsAction;

class IssuedOrder extends BaseOrder
{
    protected $table = 'orders';

    public function viewOrderItemsAction()
    {
        return ViewOrderItemsAction::make(fn(IssuedOrder $order) => view('noah-shop::tables.columns.order-items', ['order' => $order]));
    }
}
