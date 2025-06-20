<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Models\BaseOrder;

class CalculateOrderItemTotalWeight
{
    use AsAction;

    public function handle(BaseOrder $order): float
    {
        $items = $order->items;
        $total = 0;

        foreach ($items as $item) {
            $total += $item->productSpecification->weight * $item->quantity;
        }

        return $total;
    }
}
