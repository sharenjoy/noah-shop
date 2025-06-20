<div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
    <ul class="grid gap-1">
        <li><div class="font-bold text-sm" style="color: #3a3a3a">{{ $getState()->name }}</div></li>
        <li><div class="text-xs" style="color: #555555">{{ $getState()->email }}</div></li>
        <li><div class="text-xs" style="color: #555555">{{ $getState()->calling_code != '886' ? $getState()->calling_code.' ' : '' }}{{ $getState()->mobile }}</div></li>
        @if ($getState()->validOrders->count())
        <li><div class="w-fit"><x-filament::badge size="sm" color="gray">訂單 {{ $getState()->validOrders->count() }}</x-filament::badge></div></li>
        @endif
        @if($getState()->tags->count())
        <li class="py-1">
            @foreach ($getState()->tags as $tag)
            <div class="w-fit"><x-filament::badge size="sm" color="gray">{{ $tag->name }}</x-filament::badge></div>
            @endforeach
        </li>
        @endif
    </ul>
</div>
