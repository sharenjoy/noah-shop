<div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
    <ul class="grid gap-1">
        <li class="flex justify-start gap-1 py-1">
            <div class="w-fit"><x-filament::badge size="sm" color="gray">{{ $getState()->type->getLabel() }}</x-filament::badge></div>
        </li>
        @if ($getState()->type == \Sharenjoy\NoahShop\Enums\InvoiceType::Donate)
            <li><div class="text-xs" style="color: #555">{!! config('noah-cms.donate_code.' . $getState()->donate_code) !!}</div></li>
        @elseif ($getState()->type == \Sharenjoy\NoahShop\Enums\InvoiceType::Holder)
            <li class="flex justify-start gap-1 py-1">
                <div class="w-fit"><x-filament::badge size="sm" color="gray">{{ $getState()->holder_type->getLabel() }}</x-filament::badge></div>
            </li>
            <li><div class="text-xs" style="color: #555">{!! ' '.$getState()->holder_code !!}</div></li>
        @elseif ($getState()->type == \Sharenjoy\NoahShop\Enums\InvoiceType::Company)
            <li><div class="text-xs" style="color: #555">{!! $getState()->company_code. '<br>'. $getState()->company_title !!}</div></li>
        @endif
    </ul>
</div>
