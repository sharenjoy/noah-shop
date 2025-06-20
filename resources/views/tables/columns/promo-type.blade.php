<div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
    @php
        $state = $getState();
        $record = $getRecord();
    @endphp

    @if ($state == \Sharenjoy\NoahShop\Enums\PromoType::Coupon)
        <div class="w-fit">
            <div class="flex gap-1">
                <x-filament::badge iconSize="Large" color="danger">{{ $state->getLabel() }}</x-filament::badge>
                <x-filament::badge iconSize="Large" color="info">code: {{ $record->code }}</x-filament::badge>
            </div>
        </div>
    @elseif ($state == \Sharenjoy\NoahShop\Enums\PromoType::MinSpend)
        <div class="w-fit">
            <div class="flex gap-1">
                <x-filament::badge iconSize="Large" color="danger">{{ $state->getLabel() }}</x-filament::badge>
                <x-filament::badge iconSize="Large" color="info">滿金額 ${{ $record->min_spend }}</x-filament::badge>
            </div>
        </div>
    @elseif ($state == \Sharenjoy\NoahShop\Enums\PromoType::MinQuantity)
        <div class="w-fit">
            <div class="flex gap-1">
                <x-filament::badge iconSize="Large" color="danger">{{ $state->getLabel() }}</x-filament::badge>
                <x-filament::badge iconSize="Large" color="info">滿 {{ $record->min_quantity }} 件</x-filament::badge>
            </div>
        </div>
    @endif

    <ul class="grid gap-1">
        <li><div class="text-sm" style="color: #3a3a3a">最低要求訂單金額 <span class="font-bold">${{ $record->min_order_amount }}</span></div></li>
        <li><div class="text-sm" style="color: #3a3a3a">
            <div class="flex gap-1">
                <div>
                    {{ $record->discount_type->getLabel() }}
                </div>
                <div class="font-bold">
                    @if ($record->discount_type->value == \Sharenjoy\NoahShop\Enums\PromoDiscountType::Percent->value)
                    {{ $record->discount_percent }}%
                    @elseif ($record->discount_type->value == \Sharenjoy\NoahShop\Enums\PromoDiscountType::Amount->value)
                    ${{ $record->discount_amount }}
                    @endif
                </div>
            </div>
        </div></li>
        @if ($record->discount_type->value == \Sharenjoy\NoahShop\Enums\PromoDiscountType::Percent->value)
            <li><div class="text-sm" style="color: #3a3a3a">折扣上限金額 <span class="font-bold">${{ $record->discount_percent_limit_amount }}</span></div></li>
        @endif
    </ul>
</div>
