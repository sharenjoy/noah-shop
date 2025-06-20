<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Actions\Shop\CalculatePricesAndUpdateInvoice;
use Sharenjoy\NoahShop\Actions\Shop\InvoicePricesCreator;
use Sharenjoy\NoahShop\Enums\OrderShipmentStatus;
use Sharenjoy\NoahShop\Enums\OrderStatus;
use Sharenjoy\NoahShop\Enums\TransactionStatus;
use Sharenjoy\NoahShop\Models\Order;

class OrderCreator
{
    use AsAction;

    public function handle(array $data): Order
    {
        $order = Order::create([
            'user_id' => $data['user_id'] ?? Auth::user()->id,
            'status' => OrderStatus::New,
            'is_become_member' => $data['is_become_member'] ?? false,
            'notes' => $data['notes'] ?? null,
        ]);

        $shipment = $order->shipments()->create(array_merge([
            'status' => OrderShipmentStatus::New,
        ], $data['shipment']));

        $currency = head($data['items'])['currency'] ?? 'TWD';

        foreach ($data['items'] as $item) {
            $specData = array_merge(Arr::except($item['productSpecification']->toArray(), ['id', 'product_id', 'order_column', 'is_active', 'created_at', 'updated_at']), [
                'product_name' => $item['product']['title'],
                'product_image' => $item['product']['img'],
            ]);

            $comparePrice = $item['productSpecification']['compare_price'] ?? 0;
            $discount = 0;
            $productPrice = $item['price'];

            if ($comparePrice > $productPrice) {
                $discount = $item['price'] - $comparePrice;
                $productPrice = $comparePrice;
            }

            $order->items()->create([
                'product_id' => $item['product']['id'],
                'product_specification_id' => $item['product_specification_id'],
                'order_shipment_id' => $shipment->id,
                'type' => 'product',
                'quantity' => $item['quantity'],
                'price' => $productPrice,
                'discount' => $discount,
                'currency' => $currency,
                'product_details' => $specData,
            ]);
        }

        $invoice = $order->invoice()->create(array_merge([
            'currency' => $currency,
        ], $data['invoice']));

        InvoicePricesCreator::run($invoice, $order);
        CalculatePricesAndUpdateInvoice::run($invoice);

        $order->transaction()->create(array_merge([
            'invoice_id' => $invoice->id,
            'status' => TransactionStatus::New,
            'total_price' => $invoice->total_price,
            'currency' => $currency,
        ], $data['transaction']));

        return $order;
    }
}
