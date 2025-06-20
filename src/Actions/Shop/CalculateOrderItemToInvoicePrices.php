<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Enums\InvoicePriceType;
use Sharenjoy\NoahShop\Models\Order;

class CalculateOrderItemToInvoicePrices
{
    use AsAction;

    public function handle(Order $order, $priceType): float
    {
        $items = $order->items;
        $amount = 0;

        if ($priceType == InvoicePriceType::Product) {
            foreach ($items as $item) {
                $amount += $item->price * $item->quantity;
            }

            return $amount;
        }

        if ($priceType == InvoicePriceType::ProductDiscount) {
            foreach ($items as $item) {
                $amount += $item->discount * $item->quantity;
            }

            return $amount;
        }

        throw new \InvalidArgumentException('Invalid price type provided.');
    }
}
