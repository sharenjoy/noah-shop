<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    <div class="fi-ta-text grid w-full gap-y-1 px-1 py-1 max-w-full overflow-x-auto">
        <ul class="divide-y divide-gray-200">
            @foreach ($getRecord()->items as $item)

            @php
                $details = $item->product_details;
            @endphp

            <li class="flex items-center py-2 item-list">
                <img src="{{ \Sharenjoy\NoahCms\Utils\Media::imgUrl($details['img']) ?? asset('vendor/noah-cms/images/placeholder.svg') }}" class="w-16 h-16 object-cover rounded mr-4">
                <div class="flex-1 p-2">
                    <h3 class="text-sm font-medium text-gray-700">{{ $details['product_name'] }}</h3>
                </div>
                <div class="flex-1 p-2">
                    <h3 class="text-sm font-medium text-gray-700">{!! join(', ', $details['spec_detail_name']) !!}</h3>
                </div>
                <div class="qty-col p-2 text-right">
                    <h3 class="text-sm font-medium text-gray-700">Qty: {{ $item->quantity }}</h3>
                </div>
                <div class="price-col p-2 text-right">
                    <h3 class="text-sm font-medium text-gray-700">{{ currency_format($item->subtotal, $item->currency) }}</h3>
                </div>
            </li>
            @endforeach
        </ul>
    </div>

</x-dynamic-component>

<style>
    li.item-list:nth-child(odd) {
        background-color: #ffffff; /* 白色背景 */
    }

    li.item-list:nth-child(even) {
        background-color: #fbfbfb; /* 淺灰色背景 */
    }
    .qty-col, .price-col {
        width: 8em;
    }
</style>
