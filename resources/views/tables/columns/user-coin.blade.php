<div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
    @php
        $user = $getRecord();
    @endphp
    <ul class="grid gap-1">
        @if (\Sharenjoy\NoahCms\Actions\Shop\ShopFeatured::run('coin-point'))
        <li><div class="font-bold text-sm" style="color: #3a3a3a">點數 {{ $user->point }} 點</div></li>
        @endif
        @if (\Sharenjoy\NoahCms\Actions\Shop\ShopFeatured::run('coin-shoppingmoney'))
        <li><div class="font-bold text-sm" style="color: #3a3a3a">購物金 {{ number_format($user->shoppingmoney) }} 元</div></li>
        @endif
    </ul>
</div>
