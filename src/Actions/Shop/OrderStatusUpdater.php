<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Enums\OrderStatus;
use Sharenjoy\NoahShop\Models\BaseOrder;

class OrderStatusUpdater
{
    use AsAction;

    public function handle(BaseOrder $order, OrderStatus $targetStatus, ?string $notes = null): bool
    {
        [$canUpdate, $message] = OrderStatusChecker::canUpdate($order, $targetStatus);

        if (! $canUpdate) {
            Notification::make()
                ->title('無法更新訂單狀態')
                ->body($message)
                ->danger()
                ->send();

            return false;
        }

        activity()->disableLogging();

        $old = $order->toArray();
        $order->update(['status' => $targetStatus]);

        activity()->enableLogging();

        activity()
            ->useLog('noah-shop')
            ->performedOn($order)
            ->causedBy(Auth::user())
            ->withProperties(['old' => $old, 'attributes' => $order->toArray(), 'notes' => $notes])
            ->event('updated-order-status')
            ->log('updated-order-status');

        Notification::make()
            ->title('訂單狀態更新成功至 ' . $order->status->getLabel())
            ->success()
            ->send();

        return true;
    }
}
