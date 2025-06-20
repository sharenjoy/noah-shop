<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Lorisleiva\Actions\Concerns\AsAction;
use Sharenjoy\NoahShop\Enums\OrderShipmentStatus;
use Sharenjoy\NoahShop\Enums\OrderStatus;
use Sharenjoy\NoahShop\Enums\TransactionStatus;
use Sharenjoy\NoahShop\Models\BaseOrder;

class OrderStatusChecker
{
    use AsAction;

    public function handle(BaseOrder $order, OrderStatus $targetStatus): array
    {
        $scope = $order->getCurrentScope();

        [$allowed, $message] = $this->checkAllowedTransitions($scope, $targetStatus);

        if ($allowed !== true) {
            return [$allowed, $message];
        }

        if ($targetStatus === OrderStatus::Processing) {
            if (empty($order->shipment)) {
                return [false, '訂單尚未建立出貨資訊，無法轉換為 ' . OrderStatus::Processing->getLabel() . '。'];
            }

            if (! in_array($order->shipment->status, [
                OrderShipmentStatus::New,
                OrderShipmentStatus::Shipped,
                OrderShipmentStatus::Delivered,
                OrderShipmentStatus::Returning,
                OrderShipmentStatus::Returned,
            ])) {
                return [false, '出貨資訊狀態不符合要求，無法轉換至 ' . OrderStatus::Processing->getLabel() . '。'];
            }

            if ($order->transaction && ! in_array($order->transaction->status, [
                TransactionStatus::Paid,
                TransactionStatus::Refunding,
                TransactionStatus::Refunded,
            ])) {
                return [false, '交易資訊狀態不正確，無法轉換至 ' . OrderStatus::Processing->getLabel() . '。交易狀態必須是已付款、退款中、已退款的其中之一'];
            }

            if (! in_array($scope, ['new', 'shippable', 'shipped', 'delivered', 'issued', 'pending'])) {
                return [false, "訂單必須是可出貨、已出貨、已送達、退貨/退款/取消中的其中之一，才能轉換為 " . OrderStatus::Processing->getLabel() . '。'];
            }
        }

        return [true, null];
    }

    public static function canUpdate(BaseOrder $order, OrderStatus $targetStatus): array
    {
        return (new static())->handle($order, $targetStatus);
    }

    protected function checkAllowedTransitions($currentScope, $targetStatus): array
    {
        // 定義允許的狀態轉換規則
        $allowedTransitions = [
            'new' => [OrderStatus::Processing, OrderStatus::Pending, OrderStatus::Cancelled],
            'processing' => [OrderStatus::Pending, OrderStatus::Cancelled],
            'pending' => [OrderStatus::Processing, OrderStatus::Cancelled],
            'cancelled' => [],
            'finished' => [],
        ];

        // 如果當前 scope 不存在，則不允許更新
        if (! $currentScope) {
            return [false, []];
        }

        // 檢查目標狀態是否在允許的轉換列表中
        $isAllowed = in_array($targetStatus, $allowedTransitions[$currentScope] ?? []);

        // 將允許的狀態轉換為對應的 Enum Label
        $allowedLabels = array_map(fn($status) => $status->getLabel(), $allowedTransitions[$currentScope] ?? []);

        if ($isAllowed === false) {
            if (count($allowedLabels) === 0) {
                return [false, "無法更新狀態至 " . $targetStatus->getLabel() . "。"];
            }

            $allowedStatusesText = implode('、', $allowedLabels);
            return [false, "目標訂單狀態必須是以下狀態之一才能轉換：{$allowedStatusesText}。"];
        }

        return [$isAllowed, null];
    }
}
