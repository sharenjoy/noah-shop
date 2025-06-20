<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Enums\DeliveryType;
use Sharenjoy\NoahShop\Enums\InvoicePriceType;
use Sharenjoy\NoahShop\Models\Invoice;
use Sharenjoy\NoahShop\Models\Order;

class InvoicePricesCreator
{
    use AsAction;

    protected array $prices = [];

    public function handle(Invoice $invoice, Order $order): void
    {
        $productPrice = CalculateOrderItemToInvoicePrices::run($order, InvoicePriceType::Product);
        $productDiscount = CalculateOrderItemToInvoicePrices::run($order, InvoicePriceType::ProductDiscount);

        $this->prices[] = [
            'order_id' => $order->id,
            'type' => 'product',
            'value' => $productPrice,
        ];

        $this->prices[] = [
            'order_id' => $order->id,
            'type' => 'product_discount',
            'value' => $productDiscount,
        ];

        $total = $productPrice + $productDiscount;
        $delivery = (int)setting('order.delivery_price')['國內'];

        if ($order->shipment->type == DeliveryType::Address && $order->shipment->country != 'Taiwan') {
            $delivery = (int)setting('order.delivery_price')['國外'];
        }

        if ($total >= setting('order.delivery_free_limit')) {
            $delivery = 0;
        }

        $this->prices[] = [
            'order_id' => $order->id,
            'type' => InvoicePriceType::Delivery,
            'value' => $delivery,
        ];

        $this->prices[] = [
            'order_id' => $order->id,
            'type' => InvoicePriceType::Shoppingmoney,
            // TODO
            'value' => $order['shoppingmoney'] ?? 0,
        ];

        $this->prices[] = [
            'order_id' => $order->id,
            'type' => InvoicePriceType::Point,
            // TODO
            'value' => $order['point'] ?? 0,
            'content' => null,
        ];

        foreach ($this->prices as $price) {
            $invoice->prices()->create($price);
        }
    }
}
