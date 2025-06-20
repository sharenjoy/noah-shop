<x-noah-cms::order-docs>

    <div id="order-info-area">
        @foreach ($models as $order)
        <div class="print-content">
            <div style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; max-width: 1000px; margin: 0 auto;">
                <!-- Header -->
                <table style="width: 100%; border-bottom: 1px solid #e5e7eb; padding-bottom: 16px;">
                    <tr>
                        <td>
                            <h1 style="font-size: 24px; font-weight: bold; color: #4a5568; margin-bottom: 5px;">訂單明細</h1>
                            <p style="color: #5f6b7d;"># {{ $order->sn }}</p>
                            <p style="color: #5f6b7d; margin-bottom: 5px;">{{ $order->created_at }}</p>
                        </td>
                        <td style="text-align: right; vertical-align: top;">
                            <img src="{{ media_url(noah_setting('general.logo')) ?? url('vendor/noah-cms/images/logo-placeholder.png') }}" style="margin-left: auto; height: 64px;">
                            <p style="color: #5f6b7d; margin-top: 5px;">{{ noah_setting('general.app_name') }}</p>
                            <p style="color: #5f6b7d; margin-bottom: 10px;">{{ noah_setting('general.contact_email') }}</p>
                        </td>
                    </tr>
                </table>

                <!-- Billing Information -->
                <table style="width: 100%; margin-top: 24px;">
                    <tr>
                        <td>
                            <h2 style="font-weight: bold; color: #4a5568;">收件人資訊</h2>
                            <p style="color: #718096; font-size: 14px;">{{ $order->shipment?->name }}</p>
                            <p style="color: #718096; font-size: 14px; margin-bottom: 3px;">{{ $order->shipment?->call }}</p>
                            <p style="color: #718096; font-size: 14px;">{{ $order->shipment->delivery_method }}</p>
                            <p style="color: #718096; font-size: 14px;">{!! \Sharenjoy\NoahShop\Actions\Shop\DisplayOrderShipmentDetail::run($order->shipment) !!}</p>
                        </td>
                        <td style="text-align: right;">
                            <h2 style="font-weight: bold; color: #4a5568;">訂購人資訊</h2>
                            <p style="color: #718096; font-size: 14px;">{{ $order->user?->name }}</p>
                            <p style="color: #718096; font-size: 14px;">{{ $order->user?->call }}</p>
                            <p style="color: #718096; font-size: 14px;">{{ $order->user?->email }}</p>
                        </td>
                    </tr>
                </table>

                <!-- Invoice Items -->
                <table style="width: 100%; margin-top: 24px; border-collapse: collapse; border: 1px solid #e2e8f0;">
                    <thead>
                        <tr style="background-color: #f7fafc; font-size: 13px;">
                            <th style="border: 1px solid #e2e8f0; padding: 5px 6px; text-align: left; color: #4a5568;">品號</th>
                            <th style="border: 1px solid #e2e8f0; padding: 5px 6px; text-align: left; color: #4a5568;">商品</th>
                            <th style="border: 1px solid #e2e8f0; padding: 5px 6px; text-align: right; color: #4a5568;">價格</th>
                            <th style="border: 1px solid #e2e8f0; padding: 5px 6px; text-align: right; color: #4a5568;">折扣</th>
                            <th style="border: 1px solid #e2e8f0; padding: 5px 6px; text-align: right; color: #4a5568;">數量</th>
                            <th style="border: 1px solid #e2e8f0; padding: 5px 6px; text-align: right; color: #4a5568;">小計</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr style="font-size: 13px;">
                            <td style="border: 1px solid #e2e8f0; padding: 3px 6px; color: #718096;">{{ $item->productSpecification->no }}</td>
                            <td style="border: 1px solid #e2e8f0; padding: 3px 6px; color: #718096;">{{ $item->product->title }}<br><span style="font-size: 11px;">{{ $item->productSpecification->spec }}</span></td>
                            <td style="border: 1px solid #e2e8f0; padding: 3px 6px; text-align: right; color: #718096;">{{ currency_format($item->price, $item->currency, false) }}</td>
                            <td style="border: 1px solid #e2e8f0; padding: 3px 6px; text-align: right; color: #718096;">{{ currency_format($item->discount, $item->currency, false) }}</td>
                            <td style="border: 1px solid #e2e8f0; padding: 3px 6px; text-align: right; color: #718096;">{{ $item->quantity }}</td>
                            <td style="border: 1px solid #e2e8f0; padding: 3px 6px; text-align: right; color: #718096;">{{ currency_format($item->subtotal, $item->currency) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @foreach ($order->invoicePrices as $price)
                        <tr @if($loop->first)style="background-color: #f7fafc;"@endif>
                            <td colspan="5" style="border: 1px solid #e2e8f0; padding: 3px 6px; text-align: right; color: #4a5568; font-size: 13px;">{{ $price->type->getLabel() }}</td>
                            <td style="border: 1px solid #e2e8f0; padding: 3px 6px; text-align: right; color: #4a5568; font-size: 13px;">{{ currency_format($price->value, $order->invoice->currency) }}</td>
                        </tr>
                        @endforeach
                        <tr style="background-color: #f7fafc;">
                            <td colspan="5" style="border: 1px solid #e2e8f0; padding: 3px 6px; text-align: right; font-weight: bold; color: #4a5568; font-size: 14px;">總計</td>
                            <td style="border: 1px solid #e2e8f0; padding: 3px 6px; text-align: right; font-weight: bold; color: #4a5568; font-size: 14px;">{{ currency_format($order->invoice->total_price, $order->invoice->currency) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Footer -->
                <div style="margin-top: 24px; text-align: left; color: #7e8a9a; font-size: 14px;">
                    <p>{!! nl2br(noah_setting('order.order_info_list.notice')) !!}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</x-noah-cms::order-docs>
