<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    @php
        $order = $getRecord();
        $shipment = $getRecord()->shipment;
        $transaction = $getRecord()->transaction;
        $invoice = $getRecord()->invoice;
        $user = $getRecord()->user;
    @endphp

    <div class="w-full max-w-full overflow-x-auto">
        <ul class="divide-y divide-gray-100">
            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">訂單編號</h3>
                </div>
                <div class="flex-1">
                    <h3 class="text-md font-medium text-gray-700">{{ $order->sn }}</h3>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">訂單狀態</h3>
                </div>
                <div class="flex-1">
                    <div class="w-fit">
                        <x-filament::badge
                                :color="$order->status->getColor()"
                                :icon="$order->status->getIcon()"
                                :iconSize="\Filament\Support\Enums\IconSize::Medium"
                            >
                                <div class="text-status-badge">{{ $order->status->getLabel() }}</div>
                        </x-filament::badge>
                    </div>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">訂購人</h3>
                </div>
                <div class="flex-1">
                    <ul class="grid gap-1">
                        <li><div class="font-bold text-md" style="color: #3a3a3a">{{ $user->name }}</div></li>
                        <li><div class="text-md" style="color: #6e6e6e">{{ $user->email }}</div></li>
                        <li><div class="text-md" style="color: #6e6e6e">{{ $user->phone }}</div></li>
                        @if ($user->validOrders->count())
                        <li><div class="w-fit"><x-filament::badge color="gray">訂單 {{ $user->validOrders->count() }}</x-filament::badge></div></li>
                        @endif
                        @if($user->tags->count())
                        <li class="py-1">
                            @foreach ($user->tags as $tag)
                            <div class="w-fit"><x-filament::badge color="gray">{{ $tag->name }}</x-filament::badge></div>
                            @endforeach
                        </li>
                        @endif
                    </ul>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">運送資訊</h3>
                </div>
                <div class="flex-1">
                    <ul class="grid gap-1">
                        <li><div class="font-bold text-md" style="color: #3a3a3a">{{ $shipment->name }}</div></li>
                        <li><div class="text-md" style="color: #6e6e6e">{{ $shipment->calling_code }} {{ $shipment->mobile }}</div></li>
                        <li><div class="text-sm" style="color: #6e6e6e">{{ number_format(\Sharenjoy\NoahShop\Actions\Shop\CalculateOrderItemTotalWeight::run($order)) }}(g)總重量</div></li>
                        <li class="flex justify-start gap-1 py-1">
                            <div class="w-fit">
                                <div class="flex gap-1">
                                    @if ($shipment->provider != \Sharenjoy\NoahShop\Enums\DeliveryProvider::None)
                                    <x-filament::avatar
                                        src="{{ asset('vendor/noah-cms/images/'.$shipment->provider->value.'.png') }}"
                                        size="sm"
                                    />
                                    <x-filament::badge color="gray">{{ $shipment->provider->getLabel() }}</x-filament::badge>
                                    @endif
                                    <x-filament::badge color="info">{{ $shipment->delivery_type->getLabel() }}</x-filament::badge>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="text-md" style="color: #6e6e6e"> {!! \Sharenjoy\NoahShop\Actions\Shop\DisplayOrderShipmentDetail::run(shipment: $shipment) !!}</div>
                        </li>
                        <li class="flex justify-start gap-1 pt-1">
                            <div class="w-fit"><x-filament::badge color="warning">{{ $shipment->status->getLabel() }}</x-filament::badge></div>
                        </li>
                        @if ($shipment->provider == \Sharenjoy\NoahShop\Enums\DeliveryProvider::Postoffice && $shipment->postoffice_delivery_code)
                            <li><div class="text-md" style="color: #6e6e6e">{{ __('noah-shop::noah-shop.activity.label.postoffice_delivery_code') }} {{ $shipment->postoffice_delivery_code }}</div></li>
                        @endif
                    </ul>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">付款資訊</h3>
                </div>
                <div class="flex-1">
                    <ul class="grid gap-1">
                        <li>
                            <div class="font-bold text-md" style="color: #3a3a3a">{{ currency_format($transaction->total_price, $transaction->currency) }}</div>
                        </li>
                        <li class="flex justify-start gap-1 py-1">
                            <div class="w-fit"><x-filament::badge color="gray">{{ $transaction->provider->getLabel() }}</x-filament::badge></div>
                            <div class="w-fit"><x-filament::badge color="info">{{ $transaction->payment_method->getLabel() }}</x-filament::badge></div>
                        </li>
                        @if ($transaction->payment_method == \Sharenjoy\NoahShop\Enums\PaymentMethod::ATM)
                        <li><div class="text-md" style="color: #6e6e6e">
                            {!! 'ATM '.$transaction->atm_code.'<br>到期 '.$transaction->expired_at !!}
                        </div></li>
                        @endif
                        <li class="flex justify-start gap-1 pt-1">
                            <div class="w-fit"><x-filament::badge color="danger">{{ $transaction->status->getLabel() }}</x-filament::badge></div>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">發票資訊</h3>
                </div>
                <div class="flex-1">
                    <ul class="grid gap-1">
                        <li class="flex justify-start gap-1 py-1">
                            <div class="w-fit"><x-filament::badge color="gray">{{ $invoice->type->getLabel() }}</x-filament::badge></div>
                        </li>
                        @if ($invoice->type == \Sharenjoy\NoahShop\Enums\InvoiceType::Donate)
                            <li><div class="text-md" style="color: #6e6e6e">{!! config('noah-cms.donate_code.' . $invoice->donate_code) !!}</div></li>
                        @elseif ($invoice->type == \Sharenjoy\NoahShop\Enums\InvoiceType::Holder)
                            <li class="flex justify-start gap-1 py-1">
                                <div class="w-fit"><x-filament::badge color="gray">{{ $invoice->holder_type->getLabel() }}</x-filament::badge></div>
                            </li>
                            <li><div class="text-md" style="color: #6e6e6e">{!! ' '.$invoice->holder_code !!}</div></li>
                        @elseif ($invoice->type == \Sharenjoy\NoahShop\Enums\InvoiceType::Company)
                            <li><div class="text-md" style="color: #6e6e6e">{!! $invoice->company_code. '<br>'. $invoice->company_title !!}</div></li>
                        @endif
                    </ul>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">備注</h3>
                </div>
                <div class="flex-1">
                    <h3 class="text-md font-medium text-gray-700">{!! nl2br($order->notes ?? '-') !!}</h3>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">日期</h3>
                </div>
                <div class="flex-1">
                    <div class="text-sm">
                        <div class="pb-2">建立於 {{ $order->created_at->diffForHumans() }}<br>{{ $order->created_at }}</div>
                        <div>上次更新 {{ $order->updated_at->diffForHumans() }}<br>{{ $order->updated_at }}</div>
                    </div>
                </div>
            </li>

        </ul>
    </div>

</x-dynamic-component>

<style>
    .left-col {
        width: 300px;
    }
    .text-status-badge {
        padding-left: 3px;
        font-size: 1rem;
        line-height: 1.5rem;
    }
    @media (max-width: 600px) {
        .left-col {
            width: 100px;
        }
    }
</style>
