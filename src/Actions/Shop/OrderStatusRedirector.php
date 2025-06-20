<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Filament\Facades\Filament;
use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Models\BaseOrder;

class OrderStatusRedirector
{
    use AsAction;

    public function handle(BaseOrder $order)
    {
        $resource = $order->getCurrentScope();

        if ($resource) {
            $resource .= '-orders';

            return redirect()->route('filament.' . Filament::getCurrentPanel()->getId() . '.resources.shop.' . $resource . '.view', [
                'record' => $order->id,
            ]);
        }

        return;
    }
}
