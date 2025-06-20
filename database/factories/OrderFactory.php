<?php

namespace Sharenjoy\NoahShop\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Sharenjoy\NoahShop\Actions\Shop\CalculateOrderItemToInvoicePrices;
use Sharenjoy\NoahShop\Actions\Shop\CalculatePricesAndUpdateInvoice;
use Sharenjoy\NoahShop\Enums\DeliveryProvider;
use Sharenjoy\NoahShop\Enums\DeliveryType;
use Sharenjoy\NoahShop\Enums\InvoicePriceType;
use Sharenjoy\NoahShop\Enums\OrderShipmentStatus;
use Sharenjoy\NoahShop\Enums\OrderStatus;
use Sharenjoy\NoahShop\Enums\TransactionStatus;
use Sharenjoy\NoahShop\Models\Address;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahShop\Models\Product;
use Sharenjoy\NoahShop\Models\ProductSpecification;
use Sharenjoy\NoahShop\Models\User;
use Spatie\Activitylog\Facades\LogBatch;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Sharenjoy\NoahShop\Models\Menu>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'status' => fake('en')->randomElement(OrderStatus::visibleOptions()),
            'notes' => Arr::random([fake()->sentence(), null, null]),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {

            // LogBatch::startBatch();

            activity()
                ->useLog('noah-cms')
                ->performedOn($order)
                // ->causedBy(auth()->user())
                ->withProperties(['attributes' => $order->toArray()])
                ->event('created')
                ->log('created');

            Address::factory()->count(3)->create();
            $product = Product::factory()->create();

            activity()
                ->useLog('noah-cms')
                ->performedOn($product)
                // ->causedBy(auth()->user())
                ->withProperties(['attributes' => $product->toArray()])
                ->event('created')
                ->log('created');

            $address = Address::inRandomOrder()->first();
            $specs = ProductSpecification::inRandomOrder()->limit(Arr::random([1, 2, 3, 4]))->get();

            $shipment = $order->shipments()->create([
                'status' => Arr::random(OrderShipmentStatus::visibleOptions()),
                'provider' => Arr::random(DeliveryProvider::visibleOptions()),
                'delivery_type' => Arr::random(DeliveryType::visibleOptions()),
                'name' => fake()->name(),
                'calling_code' => '886',
                'mobile' => fake()->phoneNumber(),
                'country' => $address->country,
                'postcode' => $address->postcode,
                'city' => $address->city,
                'district' => $address->district,
                'address' => $address->address,
            ]);

            activity()
                ->useLog('noah-cms')
                ->performedOn($shipment)
                // ->causedBy(auth()->user())
                ->withProperties(['attributes' => $shipment->toArray()])
                ->event('created')
                ->log('created');

            // 建立商品項目
            foreach ($specs as $spec) {
                $specData = array_merge(Arr::except($spec->toArray(), ['id', 'product_id', 'order_column', 'is_active', 'created_at', 'updated_at']), [
                    'product_name' => $spec->product->title,
                    'product_image' => $spec->product->img,
                ]);
                $item = $order->items()->create([
                    'product_id' => $spec->product->id,
                    'product_specification_id' => $spec->id,
                    'order_shipment_id' => $shipment->id,
                    'type' => 'product',
                    'quantity' => Arr::random([1, 2, 3]),
                    'price' => $spec->price ?? Arr::random([1000, 1200, 1800, 1500, 3000, 3500, 800, 1350, 3750, 300, 550]),
                    'discount' => Arr::random([0, 0, 0, 0, 0, 0, 0, -50, -100, -200]),
                    'currency' => 'TWD',
                    'product_details' => $specData,
                ]);

                activity()
                    ->useLog('noah-cms')
                    ->performedOn($item)
                    // ->causedBy(auth()->user())
                    ->withProperties(['attributes' => $item->toArray()])
                    ->event('created')
                    ->log('created');
            }

            $invoiceType = [
                [
                    'type' => 'persion',
                ],
                [
                    'type' => 'donate',
                    'donate_code' => '54321',
                ],
                [
                    'type' => 'company',
                    'company_title' => '享享創意有限公司',
                    'company_code' => '69295319',
                ],
                [
                    'type' => 'holder',
                    'holder_type' => 'mobile',
                    'holder_code' => '/R3-.2Q2',
                ],
                [
                    'type' => 'holder',
                    'holder_type' => 'certificate',
                    'holder_code' => '1234567890',
                ]
            ];

            $invoice = $order->invoice()->create(array_merge(Arr::random($invoiceType), [
                'currency' => 'TWD',
            ]));

            activity()
                ->useLog('noah-cms')
                ->performedOn($invoice)
                // ->causedBy(auth()->user())
                ->withProperties(['attributes' => $invoice->toArray()])
                ->event('created')
                ->log('created');

            $invoicePrices = [
                [
                    'order_id' => $order->id,
                    'type' => 'product',
                    'value' => CalculateOrderItemToInvoicePrices::run($order, InvoicePriceType::Product),
                ],
                [
                    'order_id' => $order->id,
                    'type' => 'product_discount',
                    'value' => CalculateOrderItemToInvoicePrices::run($order, InvoicePriceType::ProductDiscount),
                ],
                [
                    'order_id' => $order->id,
                    'type' => 'delivery',
                    'value' => Arr::random([100, 0]),
                ],
                [
                    'order_id' => $order->id,
                    'type' => 'shoppingmoney',
                    'value' => Arr::random([-100, 0, -200, -50]),
                ],
                [
                    'order_id' => $order->id,
                    'type' => 'point',
                    'value' => Arr::random([-100, 0, 0, 0, 0, 0, -200, -50]),
                    'content' => '使用點數3000點',
                ],
            ];

            foreach ($invoicePrices as $price) {
                $invoicePrice = $invoice->prices()->create($price);
                activity()
                    ->useLog('noah-cms')
                    ->performedOn($invoicePrice)
                    // ->causedBy(auth()->user())
                    ->withProperties(['attributes' => $invoicePrice->toArray()])
                    ->event('created')
                    ->log('created');
            }

            $oldInvoice = $invoice->toArray();
            CalculatePricesAndUpdateInvoice::run($invoice);

            activity()
                ->useLog('noah-cms')
                ->performedOn($invoice)
                // ->causedBy(auth()->user())
                ->withProperties(['old' => $oldInvoice, 'attributes' => Arr::except($invoice->toArray(), ['prices'])])
                ->event('updated')
                ->log('updated');

            $transaction = $order->transaction()->create([
                'invoice_id' => $invoice->id,
                'status' => 'new',
                'provider' => 'tappay',
                'payment_method' => Arr::random(['creditcard', 'atm']),
                'total_price' => $invoice->total_price,
                'currency' => $invoice->currency,
            ]);

            activity()
                ->useLog('noah-cms')
                ->performedOn($transaction)
                // ->causedBy(auth()->user())
                ->withProperties(['attributes' => $transaction->toArray()])
                ->event('created')
                ->log('created');

            // LogBatch::endBatch();
        });
    }
}
