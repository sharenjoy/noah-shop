<div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
    @php
        $state = $getState();
        $record = $getRecord();
    @endphp

    <ul class="grid gap-1">
        <li><div class="text-sm" style="color: #3a3a3a">合併其他促銷 <span class="font-bold">{{ $record->combined ? '是' : '否' }}</span></div></li>
        @if (!$record->combined)
            <li>
                <div class="flex gap-1">
                    @foreach ($record->promoTags as $tags)
                        <x-filament::badge iconSize="Medium" color="warning">{{ $tags->name }}</x-filament::badge>
                    @endforeach
                </div>
            </li>
        @endif
        @if ($record->type == \Sharenjoy\NoahShop\Enums\PromoType::Coupon)
            <li><div class="text-sm" style="color: #3a3a3a">------------</div></li>
            <li><div class="text-sm" style="color: #3a3a3a">自動產生類型 <span class="font-bold">{{ $record->auto_generate_type->getLabel() }}</span></div></li>
            @if ($record->auto_generate_type == \Sharenjoy\NoahShop\Enums\PromoAutoGenerateType::Yearly)
                <li><div class="text-sm" style="color: #3a3a3a">固定每年的幾月幾日 <span class="font-bold">{{ $record->auto_generate_date }} 日</span></div></li>
            @elseif ($record->auto_generate_type == \Sharenjoy\NoahShop\Enums\PromoAutoGenerateType::Monthly)
                <li><div class="text-sm" style="color: #3a3a3a">固定每月幾日 <span class="font-bold">{{ $record->auto_generate_day }} 號</span></div></li>
            @endif
        @endif
    </ul>
</div>
