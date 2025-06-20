<div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
    @if ($getState())
        @php
            $transaction = $getState();
        @endphp
        <ul class="grid gap-1">
            <li>
                <div class="font-bold text-sm" style="color: #3a3a3a">{{ currency_format($transaction->total_price, $transaction->currency) }}</div>
            </li>
            <li class="flex justify-start gap-1 py-1">
                <div class="w-fit"><x-filament::badge size="sm" color="gray">{{ $transaction->provider->getLabel() }}</x-filament::badge></div>
                <div class="w-fit"><x-filament::badge size="sm" color="info">{{ $transaction->payment_method->getLabel() }}</x-filament::badge></div>
            </li>
            @if ($transaction->payment_method == \Sharenjoy\NoahShop\Enums\PaymentMethod::ATM)
            <li><div class="text-xs" style="color: #555">
                {!! 'ATM '.$transaction->atm_code.'<br>到期 '.$transaction->expired_at !!}
            </div></li>
            @endif
            <li class="flex justify-start gap-1 pt-1">
                <div class="w-fit"><x-filament::badge size="sm" color="danger">{{ $transaction->status->getLabel() }}</x-filament::badge></div>
            </li>
        </ul>
    @else
        @php
            $transaction = $getRecord();
        @endphp
        <ul class="grid gap-1">
            <li>
                <div class="font-bold text-sm" style="color: #3a3a3a">{{ currency_format($transaction->total_price, $transaction->currency) }}</div>
            </li>
            @if ($transaction->payment_method == \Sharenjoy\NoahShop\Enums\PaymentMethod::ATM)
            <li><div class="text-xs" style="color: #555">
                {!! 'ATM '.$transaction->atm_code.'<br>到期 '.$transaction->expired_at !!}
            </div></li>
            @endif
        </ul>
    @endif

</div>
